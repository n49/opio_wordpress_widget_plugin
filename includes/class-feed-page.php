<?php

namespace WP_Opio_Reviews\Includes;

class Feed_Page {

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
    }

    public function register() {
        add_filter('views_edit-' . Post_Types::FEED_POST_TYPE, array($this, 'render'), 20);
    }

    public function render() {
        $feed_count = $this->feed_deserializer->get_feed_count();
        ?>
        <div class="opio-admin-feeds">
            <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php')); ?>?page=opio-builder">Create Widget</a>
            <?php if ($feed_count < 1) { ?>
            <h3 style="display:inline;vertical-align:middle;"> - First of all, create a widget to connect and show OPIO reviews through a shortcode</h3>
            <?php } ?>
        </div>
        <?php
    }
}
