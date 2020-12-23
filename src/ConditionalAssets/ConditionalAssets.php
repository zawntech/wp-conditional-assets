<?php

namespace ConditionalAssets\ConditionalAssets;

class ConditionalAssets
{
    /**
     * @param array $args
     * @return array|\WP_Post[]
     */
    public static function all( $args = [] ) {

        $args = wp_parse_args( $args, [
            'post_type' => ConditionalAssetPostType::KEY,
            'posts_per_page' => '-1',
        ]);

        $query = new \WP_Query( $args );

        $posts = array_map( function( $post ) {
            return static::prepare_post( $post );
        }, $query->posts );

        return $posts;
    }

    /**
     * Get an array of all posts as <select> options.
     * @param array $args WP Query args
     * @param string $initial_label
     * @return string[]
     */
    public static function all_as_select_options( $args = [], $initial_label = 'Select ConditionalAsset...' ) {

        $args = wp_parse_args( $args, [
            'post_type' => ConditionalAssetPostType::KEY,
            'orderby' => 'title',
            'order' => 'asc',
            'posts_per_page' => '-1',
        ] );

        $query = new \WP_Query( $args );

        $output = [
            '' => $initial_label
        ];

        foreach( $query->posts as $post ) {
            $output[(string) $post->ID] = $post->post_title;
        }

        return $output;
    }

    /**
     * @param $post
     * @return \WP_Post
     */
    public static function prepare_post( $post ) {

        if ( ! $post instanceof \WP_Post ) {
            $post = get_post( $post );
        }

        $post->_trigger_type = static::get_trigger_type( $post->ID );
        $post->_url_param_key = static::get_url_parameter_key( $post->ID );
        $post->_url_param_condition = static::get_url_parameter_condition( $post->ID );

        return $post;
    }

    /**
     * @param $key
     * @param int $post_id
     * @return mixed
     */
    protected static function get_meta( $key, $post_id = 0 ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        return get_post_meta( $post_id, $key, true );
    }

    public static function get_trigger_type( $post_id = 0 ) {
        return static::get_meta( '_trigger_type', $post_id );
    }

    public static function get_url_parameter_key( $post_id = 0 ) {
        return static::get_meta( '_url_param_key', $post_id );
    }

    public static function get_url_parameter_condition( $post_id = 0 ) {
        return static::get_meta( '_url_param_condition', $post_id );
    }

    public static function get_inline_css( $post_id = 0 ) {
        return static::get_meta( '_inline_css', $post_id );
    }

    public static function get_inline_js( $post_id = 0 ) {
        return static::get_meta( '_inline_js', $post_id );
    }
}
