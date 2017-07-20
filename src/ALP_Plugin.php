<?php
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-acadp-lower_price for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-acadp-lower_price
 * @since 1.0.0
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'ALP_Plugin' ) ) :

/**
 * Main class.
 * @since 1.0.0
 */
class ALP_Plugin {
    /**
     * @const string Plugin's version.
     * @since 1.0.0
     */
    const VERSION = '1.0.0';

    /**
     * @const string
     * @since 1.0.0
     */
    const SETTINGS_KEY = ALP_SLUG . '_settings';

    /**
     * @var array $admin_screens Array with admin screens.
     * @since 1.0.0
     */
    public static $admin_screens = [];

    /**
     * @var string
     * @since 1.0.0
     */
    public static $options_page_hook;

    /**
     * @internal Activates the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function activate() {
        //...
    }

    /**
     * @internal Deactivates the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function deactivate() {
        //...
    }

    /**
     * @return array Default values for settings of the plugin.
     * @since 1.0.0
     */
    public static function get_default_options() {
        return [
            'execution_time' => '01:00:00',
        ];
    }

    /**
     * @return array Settings of the plugin.
     * @since 1.0.0
     */
    public static function get_options() {
        $defaults = self::get_default_options();
        $options = get_option( self::SETTINGS_KEY, [] );
        $update = false;

        // Fill defaults for the options that are not set yet
        foreach( $defaults as $key => $val ) {
            if( ! array_key_exists( $key, $options ) ) {
                $options[$key] = $val;
                $update = true;
            }
        }

        // Updates options if needed
        if( $update === true) {
            update_option( self::SETTINGS_KEY, $options );
        }

        return $options;
    }

    /**
     * Returns value of option with given key.
     * @param string $key Option's key.
     * @return mixed Option's value.
     * @since 1.0.0
     * @throws Exception Whenever option with given key doesn't exist.
     */
    public static function get_option( $key ) {
        $options = self::get_options();

        if( ! array_key_exists( $key, $options ) ) {
            throw new Exception( 'Option "'.$key.'" is not set!' );
        }

        return $options[$key];
    }

    /**
     * Initializes the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function initialize() {
        register_activation_hook( __FILE__, [__CLASS__, 'activate'] );
        register_deactivation_hook( __FILE__, [__CLASS__, 'deactivate'] );
        register_uninstall_hook( __FILE__, [__CLASS__, 'uninstall'] );

        add_action( 'init', [__CLASS__, 'init'] );
        add_action( 'admin_init', [__CLASS__, 'admin_init'] );
        add_action( 'admin_menu', [__CLASS__, 'admin_menu'] );
        add_action( 'plugins_loaded', [__CLASS__, 'plugins_loaded'] );
        add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts'] );
        add_action( 'admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts'] );
    }

    /**
     * Hook for "init" action.
     * @return void
     * @since 1.0.0
     */
    public static function init() {
        // Initialize locales
        $path = ALP_PATH . 'languages';
        load_plugin_textdomain( ALP_SLUG, false, $path );

        // Initialize options
        $options = self::get_options();

        // Initialize admin screens
        self::init_screens();
        self::screens_call_method( 'init' );
    }

    /**
     * Initialize settings using <b>WordPress Settings API</b>.
     * @link https://developer.wordpress.org/plugins/settings/settings-api/
     * @return void
     * @since 1.0.0
     */
    protected static function init_settings() {
        $section1 = self::SETTINGS_KEY . '_section_1';
        add_settings_section(
                $section1,
                __( 'Nastavení času spouštění' ),
                [__CLASS__, 'render_settings_section_1'],
                ALP_SLUG
        );

        add_settings_field(
                'execution_time',
                __( 'Čas spouštění', ALP_SLUG ),
                [__CLASS__, 'render_setting_execution_time'],
                ALP_SLUG,
                $section1
        );
    }

    /**
     * Initialize admin screens.
     * @return void
     * @since 1.0.0
     */
    protected static function init_screens() {
        include( ALP_PATH . 'src/ALP_Screen_Prototype.php' );
        include( ALP_PATH . 'src/ALP_Options_Screen.php' );

        /**
         * @var ALP_Options_Screen $options_screen
         */
        $options_screen = new ALP_Options_Screen();
        self::$admin_screens[$options_screen->get_slug()] = $options_screen;

        // ...
    }

    /**
     * Hook for "admin_init" action.
     * @return void
     * @since 1.0.0
     */
    public static function admin_init() {
        register_setting( ALP_SLUG, self::SETTINGS_KEY );

        // Initialize Settings API
        self::init_settings();

        // Initialize admin screens
        self::screens_call_method( 'admin_init' );
    }

    /**
     * Hook for "admin_menu" action.
     * @return void
     * @since 1.0.0
     */
    public static function admin_menu() {
        // Call action for `admin_menu` hook on all screens.
        self::screens_call_method( 'admin_menu' );
    }

    /**
     * Hook for "admin_enqueue_scripts" action.
     * @param string $hook
     * @return void
     * @since 1.0.0
     */
    public static function admin_enqueue_scripts( $hook ) {
        wp_enqueue_script( ALP_SLUG, plugins_url( 'js/admin.js', ALP_FILE ), ['jquery'] );
        wp_localize_script( ALP_SLUG, 'odwpalp', [
            //...
        ] );
        wp_enqueue_style( ALP_SLUG, plugins_url( 'css/admin.css', ALP_FILE ) );
    }

    /**
     * Hook for "plugins_loaded" action.
     * @return void
     * @since 1.0.0
     */
    public static function plugins_loaded() {
        //...
    }

    /**
     * Hook for "wp_enqueue_scripts" action.
     * @return void
     * @since 1.0.0
     */
    public static function enqueue_scripts() {
        //wp_enqueue_script( ALP_SLUG, plugins_url( 'js/public.js', ALP_FILE ), ['jquery'] );
        //wp_localize_script( ALP_SLUG, 'odwpalp', [
        //    //...
        //] );
        //wp_enqueue_style( ALP_SLUG, plugins_url( 'css/public.css', ALP_FILE ) );
    }

    /**
     * @internal Renders the first settings section.
     * @return void
     * @since 1.0.0
     */
    public static function render_settings_section_1() {
        ob_start( function() {} );
        include( ALP_PATH . 'partials/settings-section_1.phtml' );
        echo ob_get_flush();
    }

    /**
     * @internal Renders setting `execution_time`.
     * @return void
     * @since 1.0.0
     */
    public static function render_setting_execution_time() {
        ob_start( function() {} );
        include( ALP_PATH . '/partials/setting-execution_time.phtml' );
        echo ob_get_flush();
    }

    /**
     * @internal Uninstalls the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function uninstall() {
        if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            return;
        }

        // Nothing to do...
    }

    /**
     * @internal Prints error message in correct WP amin style.
     * @param string $msg Error message.
     * @param string $type (Optional.) One of ['error','info','success','warning'].
     * @param boolean $dismissible (Optional.) Is notice dismissible?
     * @return void
     * @since 1.0.0
     */
    protected static function print_admin_notice( $msg, $type = 'info', $dismissible = true ) {
        $class = 'notice';

        if( in_array( $type, ['error','info','success','warning'] ) ) {
            $class .= ' notice-' . $type;
        } else {
            $class .= ' notice-info';
        }

        if( $dismissible === true) {
            $class .= ' s-dismissible';
        }
        
        printf( '<div class="%s"><p>%s</p></div>', $class, $msg );
    }

    /**
     * On all screens call method with given name.
     *
     * Used for calling hook's actions of the existing screens.
     * See {@see ALP_Plugin::admin_menu} for an example how is used.
     *
     * If method doesn't exist in the screen object it means that screen
     * do not provide action for the hook.
     *
     * @access private
     * @param  string  $method
     * @return void
     * @since 1.0.0
     */
    private static function screens_call_method( $method ) {
        foreach ( self::$admin_screens as $slug => $screen ) {
            if( method_exists( $screen, $method ) ) {
                    call_user_func( [ $screen, $method ] );
            }
        }
    }
}

endif;
