<?php
namespace ConditionalAssets\ConditionalAssets;

use Zawntech\WPAdminOptions\InputOption;
use Zawntech\WPAdminOptions\TextareaOption;

/**
 * Class CssOptions
 */
class CssOptionsMetaBox
{
    const ID = 'css-options';

    const TITLE = 'Inline CSS';

    public function __construct() {
        // Define which post types we want to hook.
        $post_types = ['conditional-asset'];
        foreach( $post_types as $post_type ) {
            add_action( 'add_meta_boxes_' . $post_type, [$this, 'register_meta_box'] );
            add_action( 'save_post_' . $post_type, [$this, 'save_post'] );
        }
    }

    public function register_meta_box() {
        add_meta_box( static::ID, static::TITLE, [$this, 'render_meta_box'] );
    }

    public function render_meta_box( \WP_Post $post ) {
        ?>
        <table class="form-table">
            <tbody>
            <?php
            new TextareaOption([
                'key' => '_inline_css',
                'label' => 'Inline CSS',
                'value' => ConditionalAssets::get_inline_css()
            ]);
            ?>
            </tbody>
        </table>

        <?php
        wp_enqueue_script( 'ace' );
        $this->script();
    }

    public function script() {
        ?>

        <style type="text/css" media="screen">
            #inline-css-box {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }
        </style>
        <script>
        jQuery(document).ready(function ($) {
          var textarea = $('textarea#_inline_css');
          textarea.before('<div id="inline-css-box"><?= esc_js( ConditionalAssets::get_inline_css() ); ?></div>');
          var editor = ace.edit("inline-css-box");
          editor.setTheme("ace/theme/monokai");
          editor.getSession().setMode("ace/mode/css");
          editor.getSession().on('change', function () {
            textarea.val(editor.getSession().getValue());
          });
        });
        </script>
        <?php
    }

    public function save_post( $post_id ) {

        // Stringy options
        $keys = [
            '_inline_css'
        ];

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
                update_post_meta( $post_id, $key, $value );
            }
        }

        // Json options
        $json_keys = [
        ];

        foreach( $json_keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = stripslashes( $_POST[$key] );
                $value = json_decode( $value, true );
                update_post_meta( $post_id, $key, $value );
            }
        }
    }
}
