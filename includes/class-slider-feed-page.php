<?php

namespace WP_Opio_Reviews\Includes;

class Slider_Feed_Page {

    public function __construct(Slider_Deserializer $slider_deserializer) {
        $this->slider_deserializer = $slider_deserializer;
    }

    public function register() {
        add_filter('views_edit-' . Post_Types::SLIDER_POST_TYPE, array($this, 'render'), 20);
    }

    public function render() {
        $feed_count = $this->slider_deserializer->get_feed_count();
        ?>
        <div class="opio-admin-feeds">
            <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php')); ?>?page=opio-slider">Create Widget</a>
            <?php if ($feed_count < 1) { ?>
            <h3 style="display:inline;vertical-align:middle;"> - First of all, create a widget to connect and show OPIO reviews through a shortcode</h3>
            <?php } ?>
        </div>
        <?php
    }
}
