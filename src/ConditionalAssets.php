<?php
namespace ConditionalAssets;

/**
 * Class ConditionalAssets
 *
 * This is the main plugin class which instantiates individual plugin components.
 */
class ConditionalAssets
{
    /**
     * @var ConditionalAssets;
     */
    protected static $instance;

    /**
     * Returns (and initializes once) an instance of the plugin class.
     *
     * @return ConditionalAssets
     */
    public static function get_instance() {
        if ( ! static::$instance ) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * Register components upon plugin instantiation.
     */
    protected function __construct() {
        $this->register_components();
    }

    public function register_components() {
        new Setup\SetupComponent;
        new ConditionalAssets\ConditionalAssetsComponent;
    }
}