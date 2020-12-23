<?php
namespace ConditionalAssets\ConditionalAssets;

class TriggerTypes
{
    public static function get_types() {

        $default_types = [
            'url_parameter' => 'URL Parameter',
        ];

        return apply_filters( 'conditional_assets_trigger_types', $default_types );
    }

    public static function get_label( $key ) {

        $types = static::get_types();

        if ( isset( $types[$key] ) ) {
            return $types[$key];
        }

        return $key;
    }
}