<?php

namespace ConditionalAssets\ConditionalAssets;

class ConditionalAssetLoader
{
    const TRANSIENT_KEY = 'ca_cached_assets';
    const TRANSIENT_LIFETIME = 60 * 60;

    public function __construct() {
        add_action( 'init', [$this, 'maybe_load_conditional_assets'] );
    }

    public function maybe_load_conditional_assets() {

        $data = get_transient( static::TRANSIENT_KEY );

        if ( !$data ) {
            $data = ConditionalAssets::all();
            set_transient( static::TRANSIENT_KEY, $data, static::TRANSIENT_LIFETIME );
        }

        foreach ( $data as $post ) {
            switch ( $post->_trigger_type ) {
                case 'url_parameter':
                    $this->check_url_parameter( $post );
                    break;
            }
        }
    }

    public function check_url_parameter( $post ) {

        $key = $post->_url_param_key;
        $condition = $post->_url_param_condition;

        // Skip if no key is present.
        if ( empty( $key ) ) {
            return;
        }

        $should_load = true;
        if ( 'is_present' === $condition && !isset( $_GET[$key] ) ) {
            $should_load = false;
        }
        if ( 'is_not_present' === $condition && isset( $_GET[$key] ) ) {
            $should_load = false;
        }
        if ( !$should_load ) {
            return;
        }

        $this->load_assets( $post );
    }

    protected function load_assets( $post ) {
        $inline_js = trim( $post->_inline_js );
        $inline_js_pos = $post->_inline_js_position;
        $inline_css = trim( $post->_inline_css );
        $inline_css_pos = $post->_inline_css_position;

        // Javascript
        if ( !empty( $inline_js ) ) {

            if ( 'head' === $inline_js_pos ) {
                add_action( 'wp_head', function() use ( $inline_js ) {
                    printf( '<script>%s</script>', $inline_js );
                } );
            }
            if ( 'footer' === $inline_js_pos ) {
                add_action( 'wp_footer', function() use ( $inline_js ) {
                    printf( '<script>%s</script>', $inline_js );
                } );
            }
        }

        // CSS
        if ( !empty( $inline_css ) ) {
            if ( 'head' === $inline_css_pos ) {
                add_action( 'wp_head', function() use ( $inline_css ) {
                    printf( '<style>%s</style>', $inline_css );
                } );
            }
            if ( 'footer' === $inline_css_pos ) {
                add_action( 'wp_footer', function() use ( $inline_css_pos ) {
                    printf( '<style>%s</style>', $inline_css_pos );
                } );
            }
        }
    }

    public static function purge_transients() {
        delete_transient( static::TRANSIENT_KEY );
    }
}