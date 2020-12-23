<?php
namespace ConditionalAssets\ConditionalAssets;

use Zawntech\WPAdminOptions\InputOption;
use Zawntech\WPAdminOptions\SelectOption;

class ConditionalAssetOptionsMetaBox
{
    const ID = 'conditional-asset-options';

    const TITLE = 'Conditional Asset Options';

    public function __construct() {
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

        $this->style();

        ?>
        <table class="form-table">
            <tbody>
            <?php
            new SelectOption([
                'key' => '_trigger_type',
                'label' => 'Trigger Type',
                'value' => ConditionalAssets::get_trigger_type(),
                'options' => [
                    '' => 'Select Trigger Type...',
                    'url_parameter' => 'URL Parameter'
                ]
            ]);

            new SelectOption([
                'key' => '_url_param_condition',
                'label' => 'URL Parameter Condition',
                'value' => ConditionalAssets::get_url_parameter_condition(),
                'options' => [
                    'is_present' => 'Is Present',
                    'is_not_present' => 'Is Not Present',
                ]
            ]);

            new InputOption([
                'key' => '_url_param_key',
                'label' => 'URL Parameter',
                'value' => ConditionalAssets::get_url_parameter_key(),
            ]);

            new SelectOption([
                'key' => '_inline_css_position',
                'label' => 'CSS Position',
                'value' => ConditionalAssets::get_inline_css_position(),
                'options' => [
                    '' => 'Select position...',
                    'head' => 'Head',
                    'footer' => 'Footer'
                ]
            ]);

            new SelectOption([
                'key' => '_inline_js_position',
                'label' => 'Javascript Position',
                'value' => ConditionalAssets::get_inline_js_position(),
                'options' => [
                    '' => 'Select position...',
                    'head' => 'Head',
                    'footer' => 'Footer'
                ]
            ]);
            ?>
            </tbody>
        </table>
        <?php

        $this->script();
    }

    public function script() {
        ?>
        <script>
            jQuery(document).ready(function ($) {

              var triggerType = $('select#_trigger_type'),
                urlParamKey = $('input#_url_param_key'),
                urlParamCondition = $('select#_url_param_condition');

              function getTriggerType() {
                return triggerType.val();
              }

              function processTriggerType() {
                var value = getTriggerType();
                if ('url_parameter' === value) {
                  urlParamKey.parents('tr').fadeIn();
                  urlParamCondition.parents('tr').fadeIn();
                } else {
                  urlParamKey.parents('tr').fadeOut();
                  urlParamCondition.parents('tr').fadeOut();
                }
              }

              processTriggerType();
              triggerType.on('change', processTriggerType);
            });
        </script>
        <?php
    }

    public function style() {
        ?>
        <style>
            #row-_url_param_key,
            #row-_url_param_condition {
                display: none;
            }
        </style>
        <?php
    }

    public function save_post( $post_id ) {

        ConditionalAssetLoader::purge_transients();

        // Stringy options
        $keys = [
            '_trigger_type',
            '_url_param_condition',
            '_url_param_key',
            '_inline_css_position',
            '_inline_js_position'
        ];

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
                $value = stripslashes( $value );
                $value = html_entity_decode( $value );
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
