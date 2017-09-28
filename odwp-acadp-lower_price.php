<?php
/**
 * Plugin Name: Pravidelné snižování cen
 * Plugin URI: https://github.com/ondrejd/odwp-acadp-lower_price
 * Description: Jednoduchý plugin pro <a href="https://wordpress.org/" target="blank">WordPress</a>, který pravidelně snižuje cenu inzerátů vytvořeného pomocí pluginu <a href="https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/" target="blank">Advanced Classifieds &amp; Directory Pro</a>.
 * Version: 1.0.0
 * Author: Ondřej Doněk
 * Author URI: https://ondrejd.com/
 * License: GPLv3
 * Requires at least: 4.7
 * Tested up to: 4.8.1
 * Tags: custom plugin
 * Donate link: https://www.paypal.me/ondrejd
 *
 * Text Domain: odwp-acadp-lower_price
 * Domain Path: /languages/
 *
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-acadp-lower_price for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-acadp-lower_price
 * @since 1.0.0
 */

/**
 * This file is just a bootstrap. It checks if requirements of plugins are met
 * and accordingly either initializes the plugin or halts the process.
 *
 * Requirements can be specified for PHP and the WordPress self - version
 * for both, required extensions for PHP and requireds plugins for WP.
 *
 * If you are using copy of original file in your plugin you shoud change
 * prefix "odwpalp" and name "odwp-acadp-lower_price" to your own values.
 *
 * To set the requirements go down to line 133 and define array that
 * is used as a parameter for `odwpalp_check_requirements` function.
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Some widely used constants
defined( 'ALP_SLUG' ) || define( 'ALP_SLUG', 'odwpalp' );
defined( 'ALP_NAME' ) || define( 'ALP_NAME', 'odwp-acadp-lower_price' );
defined( 'ALP_PATH' ) || define( 'ALP_PATH', dirname( __FILE__ ) . '/' );
defined( 'ALP_FILE' ) || define( 'ALP_FILE', __FILE__ );
defined( 'ALP_LOG' )  || define( 'ALP_LOG', WP_CONTENT_DIR . '/debug.log' );


if( ! function_exists( 'odwpalp_check_requirements' ) ) :
    /**
     * Checks requirements of our plugin.
     * @global string $wp_version
     * @param array $requirements
     * @return array
     * @since 1.0.0
     */
    function odwpalp_check_requirements( array $requirements ) {
        global $wp_version;

        // Initialize locales
        load_plugin_textdomain( ALP_SLUG, false, dirname( __FILE__ ) . '/languages' );

        /**
         * @var array Hold requirement errors
         */
        $errors = [];

        // Check PHP version
        if( ! empty( $requirements['php']['version'] ) ) {
            if( version_compare( phpversion(), $requirements['php']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'PHP nesplňuje nároky pluginu na minimální verzi (vyžadována nejméně <b>%s</b>)!', ALP_SLUG ),
                        $requirements['php']['version']
                );
            }
        }

        // Check PHP extensions
        if( count( $requirements['php']['extensions'] ) > 0 ) {
            foreach( $requirements['php']['extensions'] as $req_ext ) {
                if( ! extension_loaded( $req_ext ) ) {
                    $errors[] = sprintf(
                            __( 'Je vyžadováno rozšíření PHP <b>%s</b>, to ale není nainstalováno!', ALP_SLUG ),
                            $req_ext
                    );
                }
            }
        }

        // Check WP version
        if( ! empty( $requirements['wp']['version'] ) ) {
            if( version_compare( $wp_version, $requirements['wp']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'Plugin vyžaduje vyšší verzi platformy <b>WordPress</b> (minimálně <b>%s</b>)!', ALP_SLUG ),
                        $requirements['wp']['version']
                );
            }
        }

        // Check WP plugins
        if( count( $requirements['wp']['plugins'] ) > 0 ) {
            $active_plugins = (array) get_option( 'active_plugins', [] );
            foreach( $requirements['wp']['plugins'] as $req_plugin ) {
                if( ! in_array( $req_plugin, $active_plugins ) ) {
                    $errors[] = sprintf(
                            __( 'Je vyžadován plugin <b>%s</b>, ten ale není nainstalován!', ALP_SLUG ),
                            $req_plugin
                    );
                }
            }
        }

        return $errors;
    }
endif;


if( ! function_exists( 'odwpalp_deactivate_raw' ) ) :
    /**
     * Deactivate plugin by the raw way.
     * @return void
     * @since 1.0.0
     */
    function odwpalp_deactivate_raw() {
        $active_plugins = get_option( 'active_plugins' );
        $out = [];
        foreach( $active_plugins as $key => $val ) {
            if( $val != ALP_NAME . '/' . ALP_NAME . '.php' ) {
                $out[$key] = $val;
            }
        }
        update_option( 'active_plugins', $out );
    }
endif;


if( ! function_exists( 'odwpalp_error_log' ) ) :
    /**
     * @internal Write message to the `wp-content/debug.log` file.
     * @param string $message
     * @param integer $message_type (Optional.)
     * @param string $destination (Optional.)
     * @param string $extra_headers (Optional.)
     * @return void
     * @since 1.0.0
     */
    function odwpalp_error_log( string $message, int $message_type = 0, string $destination = null, string $extra_headers = '' ) {
        if( ! file_exists( ALP_LOG ) || ! is_writable( ALP_LOG ) ) {
            return;
        }

        $record = '[' . date( 'd-M-Y H:i:s', time() ) . ' UTC] ' . $message;
        file_put_contents( ALP_LOG, PHP_EOL . $record, FILE_APPEND );
    }
endif;


if( ! function_exists( 'odwpalp_write_log' ) ) :
    /**
     * Write record to the `wp-content/debug.log` file.
     * @param mixed $log
     * @return void
     * @since 1.0.0
     */
    function odwpalp_write_log( $log ) {
        if( is_array( $log ) || is_object( $log ) ) {
            odwpalp_error_log( print_r( $log, true ) );
        } else {
            odwpalp_error_log( $log );
        }
    }
endif;


if( ! function_exists( 'readonly' ) ) :
    /**
     * Prints HTML readonly attribute. It's an addition to WP original
     * functions {@see disabled()} and {@see checked()}.
     * @param mixed $value
     * @param mixed $current (Optional.) Defaultly TRUE.
     * @return string
     * @since 1.0.0
     */
    function readonly( $current, $value = true ) {
        if( $current == $value ) {
            echo ' readonly';
        }
    }
endif;


/**
 * Errors from the requirements check
 * @var array
 */
$odwpalp_errs = odwpalp_check_requirements( [
    'php' => [
        // Enter minimum PHP version you needs
        'version' => '5.6',
        // Enter extensions that your plugin needs
        'extensions' => [
            //'gd',
        ],
    ],
    'wp' => [
        // Enter minimum WP version you need
        'version' => '4.7',
        // Enter WP plugins that your plugin needs
        'plugins' => [
            'advanced-classifieds-and-directory-pro/acadp.php',
        ],
    ],
] );

// Check if requirements are met or not
if( count( $odwpalp_errs ) > 0 ) {
    // Requirements are not met
    odwpalp_deactivate_raw();

    // In administration print errors
    if( is_admin() ) {
        $err_head = __( '<b>Pravidelné snižování cen</b>: ', ALP_SLUG );
        foreach( $odwpalp_errs as $err ) {
            printf( '<div class="error"><p>%s</p></div>', $err_head . $err );
        }
    }
} else {
    // Requirements are met so initialize the plugin.
    include( ALP_PATH . 'src/ALP_Screen_Prototype.php' );
    include( ALP_PATH . 'src/ALP_Plugin.php' );
    ALP_Plugin::initialize();
}
