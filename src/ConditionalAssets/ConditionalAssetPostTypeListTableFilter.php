<?php

namespace ConditionalAssets\ConditionalAssets;

class ConditionalAssetPostTypeListTableFilter
{
    public function __construct() {
        $post_types = ['conditional-asset'];
        foreach ( $post_types as $post_type ) {
            add_filter( "manage_{$post_type}_posts_columns", [$this, 'columns'] );
            add_filter( "manage_edit-{$post_type}_sortable_columns", [$this, 'sortable_columns'] );
            add_action( "manage_{$post_type}_posts_custom_column", [$this, 'column_content'], 10, 2 );
        }
        add_action( 'pre_get_posts', [$this, 'orderby'] );

        // Custom drop down filters
        // add_action( 'pre_get_posts', [$this, 'process_filters'] );
        // add_action( 'restrict_manage_posts', [$this, 'custom_filters'], 10, 2 );
    }

    public function columns( $columns ) {
        unset( $columns['date'] );
        $columns['_trigger'] = 'Trigger';
        $columns['_css'] = 'CSS';
        $columns['_js'] = 'JS';
        return $columns;
    }

    public function sortable_columns( $columns ) {
        // $columns['_custom_column'] = 'xyz';
        return $columns;
    }

    public function column_content( $column_name, $post_id ) {
        switch ( $column_name ) {

            case '_trigger':
                $trigger = ConditionalAssets::get_trigger_type( $post_id );
                $trigger_label = TriggerTypes::get_label( $trigger );
                $param_key = ConditionalAssets::get_url_parameter_key( $post_id );
                $condition = ConditionalAssets::get_url_parameter_condition( $post_id );
                printf( '<div>%s = %s</div>', $trigger_label, $param_key );
                printf( '<div>Condition = %s</div>', $condition );
                break;

            case '_css':
                if ( !empty( trim( ConditionalAssets::get_inline_css( $post_id ) ) ) ) {
                    echo '<span style="color: green;">&check;</span>';
                }
                break;

            case '_js':
                if ( !empty( trim( ConditionalAssets::get_inline_js( $post_id ) ) ) ) {
                    echo '<span style="color: green;">&check;</span>';
                }
                break;
        }
    }

    public function orderby( \WP_Query $query ) {
        if ( !is_admin() ) {
            return;
        }

        $orderby = $query->get( 'orderby' );

        // if ( 'xyz' == $orderby ) {
        //     $query->set('meta_key','xyz');
        //     $query->set('orderby','meta_value_num');
        // }
    }

    public function custom_filters( $post_type ) {
        if ( !in_array( $post_type, $this->post_types ) ) {
            return;
        }
        ?>
        <select name="_meta_key">
            <?php
            $items = [
                '' => 'Select item...',
            ];
            foreach ( $items as $id => $title ) {
                $selected = $_GET['_meta_key'] == $id ? ' selected="selected"' : '';
                printf( '<option value="%s"%s>%s</option>', $id, $selected, $title );
            }
            ?>
        </select>
        <?php
    }

    public function process_filters( \WP_Query $query ) {
        if ( !in_array( $query->query['post_type'], $this->post_types ) ) {
            return;
        }

        if ( isset( $_GET['_meta_key'] ) && !empty( $_GET['_meta_key'] ) ) {
            $meta_value = (int) $_GET['_meta_key'];
            $meta_query = $query->get( 'meta_query' );
            if ( empty( $meta_query ) ) {
                $meta_query = [];
            }
            $meta_query[] = [
                'key' => '_meta_key',
                'value' => $meta_value,
            ];
            $query->set( 'meta_query', $meta_query );
        }
    }
}