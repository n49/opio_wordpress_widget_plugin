<?php

namespace WP_Opio_Reviews\Includes;

class Plugin_Support {

    public function __construct() {
    }

    public function register() {
        add_action('opio_admin_page_opio-support', array($this, 'init'));
        add_action('opio_admin_page_opio-support', array($this, 'render'));
    }

    public function init() {

    }

    public function render() {

        $tab = isset($_GET['opio_tab']) && strlen($_GET['opio_tab']) > 0 ? sanitize_text_field(wp_unslash($_GET['opio_tab'])) : 'fig';

        ?>
        <div class="opio-page-title">
            Support and Troubleshooting
        </div>

        <div class="opio-support-workspace">

            <div data-nav-tabs="">

                <div class="nav-tab-wrapper">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=opio-support&opio_tab=fig')); ?>" class="nav-tab<?php if ($tab == 'fig') { ?> nav-tab-active<?php } ?>">
                        <?php echo esc_html__('Full Installation Guide', 'widget-for-opio-reviews'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=opio-support&opio_tab=support')); ?>" class="nav-tab<?php if ($tab == 'support') { ?> nav-tab-active<?php } ?>">
                        <?php echo esc_html__('Support', 'widget-for-opio-reviews'); ?>
                    </a>
                </div>


                <div id="fig" class="tab-content" style="display:<?php echo $tab == 'fig' ? 'block' : 'none'?>;">
                    <h3>How to connect OPIO Reviews Feed</h3>
                    <?php include_once(dirname(OPIO_PLUGIN_FILE) . '/includes/page-setting-fig.php'); ?>
                </div>

                <div id="support" class="tab-content" style="display:<?php echo $tab == 'support' ? 'block' : 'none'?>;">
                    <h3>Most Common Questions</h3>
                    <?php include_once(dirname(OPIO_PLUGIN_FILE) . '/includes/page-setting-support.php'); ?>
                </div>

            </div>
        </div>
        <?php
    }
}
