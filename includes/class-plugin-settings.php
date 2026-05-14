<?php

namespace WP_Opio_Reviews\Includes;

class Plugin_Settings {

    const OPTION_GROUP     = 'opio_translation_settings';
    const OPT_PROVIDER     = 'opio_translation_provider';
    const OPT_AZURE_KEY    = 'opio_translation_azure_key';
    const OPT_AZURE_REGION = 'opio_translation_azure_region';
    const PAGE_SLUG        = 'opio-settings';

    /**
     * Server-side logger. Always emits. Keys are NEVER logged in full —
     * callers must mask before passing context.
     */
    public static function log($level, $message, $context = array()) {
        $line = '[OPIO settings][' . strtoupper($level) . '] ' . $message;
        if (!empty($context) && function_exists('wp_json_encode')) {
            $line .= ' ' . wp_json_encode($context);
        }
        error_log($line);
    }

    public function register() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('opio_admin_page_' . self::PAGE_SLUG, array($this, 'render'));
        add_action('admin_post_opio_reset_breaker', array($this, 'handle_reset_breaker'));
        add_action('admin_notices', array($this, 'maybe_show_reset_notice'));
    }

    public function register_settings() {
        register_setting(self::OPTION_GROUP, self::OPT_PROVIDER, array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_provider'),
            'default'           => 'mymemory',
        ));
        register_setting(self::OPTION_GROUP, self::OPT_AZURE_KEY, array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ));
        register_setting(self::OPTION_GROUP, self::OPT_AZURE_REGION, array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_region'),
            'default'           => '',
        ));

        // Log option changes so production deploys can audit who changed what
        // and when. The Azure key is masked (length + last 4 only) — never the
        // full key value.
        add_filter('pre_update_option_' . self::OPT_PROVIDER,     array($this, 'log_provider_change'),     10, 2);
        add_filter('pre_update_option_' . self::OPT_AZURE_KEY,    array($this, 'log_azure_key_change'),    10, 2);
        add_filter('pre_update_option_' . self::OPT_AZURE_REGION, array($this, 'log_azure_region_change'), 10, 2);
    }

    public function sanitize_provider($v) {
        $original = $v;
        $v = is_string($v) ? strtolower(trim($v)) : 'mymemory';
        if (!in_array($v, array('mymemory', 'azure'), true)) {
            self::log('warning', 'invalid provider input coerced to mymemory', array(
                'input'   => is_scalar($original) ? (string) $original : gettype($original),
                'user_id' => get_current_user_id(),
            ));
            return 'mymemory';
        }
        return $v;
    }

    public function sanitize_region($v) {
        $original = is_string($v) ? $v : '';
        $v        = strtolower(trim($original));
        // Azure region slug: lowercase alphanumerics only.
        $cleaned  = preg_replace('/[^a-z0-9]/', '', $v);
        if ($cleaned !== $original && $original !== '') {
            self::log('debug', 'region sanitized', array(
                'input'   => $original,
                'cleaned' => $cleaned,
            ));
        }
        return $cleaned;
    }

    public function log_provider_change($new, $old) {
        if ($new !== $old) {
            self::log('info', 'provider option changed', array(
                'from'    => is_scalar($old) ? (string) $old : null,
                'to'      => is_scalar($new) ? (string) $new : null,
                'user_id' => get_current_user_id(),
            ));
        }
        return $new;
    }

    public function log_azure_key_change($new, $old) {
        if ($new !== $old) {
            self::log('info', 'azure key option changed', array(
                'old_present' => !empty($old),
                'old_len'     => is_string($old) ? strlen($old) : 0,
                'new_present' => !empty($new),
                'new_len'     => is_string($new) ? strlen($new) : 0,
                'new_last4'   => is_string($new) && strlen($new) >= 4 ? substr($new, -4) : null,
                'user_id'     => get_current_user_id(),
            ));
        }
        return $new;
    }

    public function log_azure_region_change($new, $old) {
        if ($new !== $old) {
            self::log('info', 'azure region option changed', array(
                'from'    => is_scalar($old) ? (string) $old : null,
                'to'      => is_scalar($new) ? (string) $new : null,
                'user_id' => get_current_user_id(),
            ));
        }
        return $new;
    }

    public function render() {
        $provider = get_option(self::OPT_PROVIDER, 'mymemory');
        $key      = get_option(self::OPT_AZURE_KEY, '');
        $region   = get_option(self::OPT_AZURE_REGION, '');
        $has_key  = !empty($key);

        $breaker_unblock = get_transient(Slider_Translator::CIRCUIT_BREAKER_KEY);
        $breaker_active  = (bool) $breaker_unblock;
        ?>
        <div class="opio-page-title">Translation Settings</div>

        <div class="opio-settings-workspace" style="max-width:780px;">

            <p>Pick the translation backend used to render slider review content in non-English locales.
                <strong>MyMemory</strong> is free and zero-config but rate-limited per server IP (~100K chars/day with email).
                <strong>Azure Translator</strong> uses your own Azure resource — 2M chars/month free on the F0 tier and isolates quota per client.
            </p>

            <form method="post" action="options.php">
                <?php settings_fields(self::OPTION_GROUP); ?>
                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row">Provider</th>
                        <td>
                            <label>
                                <input type="radio" name="<?php echo esc_attr(self::OPT_PROVIDER); ?>" value="mymemory" <?php checked($provider, 'mymemory'); ?>>
                                MyMemory (default, free, no setup)
                            </label><br>
                            <label>
                                <input type="radio" name="<?php echo esc_attr(self::OPT_PROVIDER); ?>" value="azure" <?php checked($provider, 'azure'); ?>>
                                Azure Translator (requires key below)
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="opio_azure_key">Azure Subscription Key</label></th>
                        <td>
                            <input type="password" id="opio_azure_key" name="<?php echo esc_attr(self::OPT_AZURE_KEY); ?>" value="<?php echo esc_attr($key); ?>" autocomplete="new-password" class="regular-text" placeholder="Paste KEY 1 from Azure Portal">
                            <p class="description">
                                Azure Portal → your Translator resource → <strong>Keys and Endpoint</strong> → copy <strong>KEY 1</strong>.
                                <?php if ($has_key): ?>
                                    <br>Currently saved: <code>••••<?php echo esc_html(substr($key, -4)); ?></code> (clear field and save to remove).
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="opio_azure_region">Azure Region</label></th>
                        <td>
                            <input type="text" id="opio_azure_region" name="<?php echo esc_attr(self::OPT_AZURE_REGION); ?>" value="<?php echo esc_attr($region); ?>" class="regular-text" placeholder="e.g. eastus">
                            <p class="description">
                                The <strong>Location</strong> shown on the Azure resource overview, lowercased and without spaces (e.g. <code>eastus</code>, <code>canadacentral</code>, <code>westeurope</code>).
                                Leave blank only for the global endpoint.
                            </p>
                        </td>
                    </tr>

                </table>
                <?php submit_button('Save Settings'); ?>
            </form>

            <hr>

            <h3>Rate-Limit Breaker</h3>
            <p>
                When the translation API hits a rate limit or quota cap, the plugin opens a 1-hour circuit breaker so the slider doesn't hammer the API with doomed requests. Status:
                <?php if ($breaker_active): ?>
                    <strong style="color:#a00;">Active</strong> &mdash; blocked until <?php echo esc_html(gmdate('Y-m-d H:i:s', (int) $breaker_unblock)); ?> UTC.
                <?php else: ?>
                    <strong style="color:#0a0;">Clear</strong> &mdash; translation API calls running normally.
                <?php endif; ?>
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="opio_reset_breaker">
                <?php wp_nonce_field('opio_reset_breaker'); ?>
                <?php submit_button('Reset Breaker Now', 'secondary', 'submit', false); ?>
            </form>

        </div>
        <?php
    }

    public function handle_reset_breaker() {
        if (!current_user_can('manage_options')) {
            self::log('error', 'breaker reset denied: insufficient capabilities', array(
                'user_id' => get_current_user_id(),
                'ip'      => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : null,
            ));
            wp_die('Insufficient permissions.');
        }
        check_admin_referer('opio_reset_breaker');

        $previous_unblock = get_transient(Slider_Translator::CIRCUIT_BREAKER_KEY);
        delete_transient(Slider_Translator::CIRCUIT_BREAKER_KEY);

        self::log('info', 'breaker reset', array(
            'was_active'          => (bool) $previous_unblock,
            'previous_unblock_at' => $previous_unblock ? (int) $previous_unblock : null,
            'previous_unblock_iso'=> $previous_unblock ? gmdate('c', (int) $previous_unblock) : null,
            'user_id'             => get_current_user_id(),
        ));

        wp_safe_redirect(add_query_arg(
            array('opio_breaker_reset' => '1'),
            admin_url('admin.php?page=' . self::PAGE_SLUG)
        ));
        exit;
    }

    public function maybe_show_reset_notice() {
        if (!isset($_GET['page']) || $_GET['page'] !== self::PAGE_SLUG) {
            return;
        }
        if (isset($_GET['opio_breaker_reset']) && $_GET['opio_breaker_reset'] === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>Translation rate-limit breaker reset. API calls will resume on the next slider render.</p></div>';
        }
    }

    /**
     * Bridge wp_options into Slider_Translator filters so the admin UI works
     * without each site needing functions.php snippets. Code filters in
     * functions.php run after these and still win when present.
     */
    public static function register_filter_bridges() {
        add_filter('opio_translation_provider', function($default) {
            $opt = get_option(self::OPT_PROVIDER);
            return !empty($opt) ? $opt : $default;
        });
        add_filter('opio_translation_azure_key', function($default) {
            $opt = get_option(self::OPT_AZURE_KEY);
            return !empty($opt) ? $opt : $default;
        });
        add_filter('opio_translation_azure_region', function($default) {
            $opt = get_option(self::OPT_AZURE_REGION);
            return !empty($opt) ? $opt : $default;
        });
    }
}
