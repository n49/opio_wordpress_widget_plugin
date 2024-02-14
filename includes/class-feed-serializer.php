<?php

namespace WP_Opio_Reviews\Includes;

class Feed_Serializer {

    public function __construct() {
        add_action('admin_post_' . Post_Types::FEED_POST_TYPE . '_save', array($this, 'feed_save'), 30);
    }

    public function feed_save() {
   
    
        // verify the nonce
        if (!isset($_POST[Post_Types::FEED_POST_TYPE . '_nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST[Post_Types::FEED_POST_TYPE . '_nonce'])), Post_Types::FEED_POST_TYPE . '_save')) {
            wp_die('Sorry! You are not allowed to access this page.');
        }


        // sanitize the data
        $raw_data_array = array_map('sanitize_text_field', $_POST[Post_Types::FEED_POST_TYPE]);
        // validate the data
        if (!in_array($raw_data_array['review_enabled'], array('yes', 'no'))) {
            wp_die('Sorry! You are not allowed to access this page.');
        }
        // review type should be either single, multiple or orgfeed
        if (!in_array($raw_data_array['review_type'], array('single', 'multiple', 'orgfeed'))) {
            wp_die('Sorry! You are not allowed to access this page.');
        }
        // review_option should be either opio or all
        if (!in_array($raw_data_array['review_option'], array('opio', 'all'))) {
            wp_die('Sorry! You are not allowed to access this page.');
        }
        // either biz id or org id should be set
        if (empty($raw_data_array['biz_id']) && empty($raw_data_array['org_id'])) {
            wp_die('Sorry! You need to enter either a business id or an organization id.');
        }
        // title cannot be empty
        if (empty($raw_data_array['title'])) {
            wp_die('Sorry! Review feed title is necessary.');
        }

        // sanitize the data, everything is a string
        $raw_data_array = array_map('sanitize_text_field', $raw_data_array);

        $decodedBizs = '';
        if(isset($raw_data_array['loaded_bizs'])) {
            $decodedBizs = json_decode($raw_data_array["loaded_bizs"]);
        }
        $content = json_encode(array(
            'biz_id' => $raw_data_array['biz_id'],
            'org_id' => $raw_data_array['org_id'],
            'biz_org_id' => $raw_data_array['biz_org_id'],
            'review_type' => $raw_data_array['review_type'],
            'review_option' => $raw_data_array['review_option'],
            'review_enabled' => $raw_data_array['review_enabled'],
            'loaded_bizs' => $decodedBizs
        ));
        $post_id = wp_insert_post(array(
            'ID'           => $raw_data_array['post_id'],
            'post_title'   => $raw_data_array['title'],
            'post_content' => $content,
            'post_type'    => Post_Types::FEED_POST_TYPE,
            'post_status'  => 'publish',
        ));

        // NOT: $referer = empty(wp_get_referer()) ? $raw_data_array['current_url'] : wp_get_referer();
        // COZ: Fatal error: Can't use function return value in write context in .../includes/class-feed-serializer.php on line ...
        $referer = wp_get_referer();
        $referer = empty($referer) ? $raw_data_array['current_url'] : wp_get_referer();

        wp_safe_redirect(
            add_query_arg(array(
                Post_Types::FEED_POST_TYPE . '_id' => $post_id,
            ), $referer)
        );
        exit;
    }

}
