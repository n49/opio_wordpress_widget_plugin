<?php

namespace WP_Opio_Reviews\Includes;

class Feed_Deserializer {

    public function __construct(\WP_Query $wp_query) {
        $this->wp_query = $wp_query;
    }

    public function prepareString($str) {
        // replace rgba with hex for wp_kses
        $str = preg_replace_callback('/style=".*?"/', function($matches) {
            $style = $matches[0];
            $style = preg_replace_callback('/rgba\((.*?)\)/', function($matches) {
                $rgba = $matches[1];
                $rgba = explode(',', $rgba);
                $hex = '#';
                for ($i = 0; $i < 3; $i++) {
                    $hex .= str_pad(dechex($rgba[$i]), 2, '0', STR_PAD_LEFT);
                }
                return $hex;
            }, $style);
            return $style;
        }, $str);


        add_filter( 'safe_style_css', function( $styles ) {
            $styles[] = 'color';
            // dd($styles);
            $styles[] = 'display';
            $styles[] = 'width';
            $styles[] = 'white-space';
            $styles[] = 'max-width';
            $styles[] = 'background-image';
            $styles[] = 'background-color';
            $styles[] = 'background-size';
            $styles[] = 'background-position';
            $styles[] = 'background-repeat';
            $styles[] = 'background';
            $styles[] = 'height';
            $styles[] = 'font-size';
            $styles[] = 'all';
            $styles[] = 'url';
            $styles[] = 'float';
            return $styles;
        });

        return $str;

    }

    public function get_allowed_tags() {
        // within style tags, for the color attribute, change rgba to hex

        $allowed_tags = array(
            'div' => array(
                'id' => true,
                'style' => true,
                'class' => true,
                'data-glide-el' => true,
                'onclick' => true,
            ),
            'section' => array(),
            'a' => array(
                'href' => true,
                'target' => true,
                'style' => true,
                'class' => true,
                'color' => true,
            ),
            'img' => array(
                'id' => true,
                'class' => true,
                'data-glide-el' => true,
                'src' => true,
                'style' => true,
                'alt' => true
            ),
            'style' => array(),
            'svg' => array(
                'xmlns' => true,
                'viewbox' => true,
                'style' => true
            ),
            'path' => array(
                'class' => true,
                'd' => true
            ),
            'span' => array(
                'style' => true,
            ),
            'h1' => array(),
            'p' => array(
                'style' => true,
                'class' => true
            ),
            'strong' => array(),
            'em' => array(),
            'br' => array(),
            'ul' => array(
                'style' => true,
                'class' => true
            ),
            'li' => array(),
            'script' => array(
                'src' => true,
                'type' => true,
                'id' => true,
            ),
            'head' => array(),
            'link' => array(
                'href' => true,
                'rel' => true,
                'type' => true,
                'media' => true,
                'id' => true,
            ),
            'button' => array(
                'id' => true,
                'style' => true,
                'class' => true,
                'data-glide-dir' => true,
                '<' => true,
                '>' => true,
                'data-glide-el' => true,
                'onclick' => true,
            ),
            '&gt;' => array(),
            '&lt;' => array(),
            'path' => array(
                'class' => true,
                'd' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
            ),
            'g' => array(
                'id' => true,
                'data-name' => true,
            ),
            'defs' => array(),
            'title' => array(),
            'video' => array(
                'id' => true,
                'class' => true,
                'data-glide-el' => true,
                'src' => true,
                'style' => true,
                'alt' => true,
                'autoplay' => true,
                'loop' => true,
                'muted' => true,
                'playsinline' => true,
                'preload' => true,
                'poster' => true,
                'controls' => true,
            ),
            'br' => array(),

            'source' => array(
                'src' => true,
                'type' => true,
            ),
            // Add more elements and attributes as needed
        );

        return $allowed_tags;
    }

    public function get_feed($post_id, $args = array()) {
        $default_args = array(
            'post_type'      => Post_Types::FEED_POST_TYPE,
            'p'              => $post_id,
            'posts_per_page' => -1,
            'no_found_rows'  => true,
        );
        $args = wp_parse_args($args, $default_args);
        $this->wp_query->query($args);

        if (!$this->wp_query->have_posts()) {
            return false;
        }

        return $this->wp_query->posts[0];
    }

    public function get_feed_count($args = array()) {
        $default_args = array(
            'post_type'      => Post_Types::FEED_POST_TYPE,
            'posts_per_page' => -1,
            'no_found_rows'  => true,
        );

        $args = wp_parse_args($args, $default_args);
        $this->wp_query->query($args);

        if (!$this->wp_query->have_posts()) {
            return 0;
        }

        return count($this->wp_query->posts);
    }

}
