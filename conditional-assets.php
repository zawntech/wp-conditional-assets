<?php
/*
Plugin Name: Conditional Assets
Plugin URI: https://zawntech.com
Description: Load custom CSS and JS based on conditional rule sets.
Author: Zawntech
Version: 0.0.1
Author URI: https://zawntech.com
*/

/** Absolute path to plugin directory (with trailing slash). */
define( 'CONDITIONAL_ASSETS_DIR', trailingslashit( __DIR__ ) );

/** Public URL to plugin directory (with trailing slash). */
define( 'CONDITIONAL_ASSETS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/** Plugin text domain. */
define( 'CONDITIONAL_ASSETS_TEXT_DOMAIN', 'conditional-assets' );

/** Plugin version. */
define( 'CONDITIONAL_ASSETS_VERSION', '0.0.1' );

// Verify composer autoloader is installed.
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    add_action( 'admin_notices', function() {
        $class = 'notice notice-error';
        $message = __( 'Error: the composer autoloader does not exist for Conditional Assets', CONDITIONAL_ASSETS_TEXT_DOMAIN );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    });
    return;
}

// Load composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize plugin.
ConditionalAssets\ConditionalAssetsPlugin::get_instance();

register_activation_hook( __FILE__, function() {
    $option_key = 'CONDITIONAL_ASSETS_ACTIVATE';
    update_option( $option_key, 1 );
});

register_deactivation_hook( __FILE__, function() {
    new ConditionalAssets\Setup\DeactivatePlugin;
});
