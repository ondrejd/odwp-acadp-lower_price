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
     * @const string
     * @since 1.0.0
     */
    const CRON_EVENT_KEY = ALP_SLUG . '-daily_cron';

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
        self::reset_wpcron_scheduled();
    }

    /**
     * @internal Deactivates the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function deactivate() {
        self::reset_wpcron_scheduled( true );
    }

    /**
     * @return array Default values for settings of the plugin.
     * @since 1.0.0
     */
    public static function get_default_options() {
        return [
            'execution_time' => '01:00',
            'last_execution_time' => '1970-01-01 00:00:00',
            'save_log' => true,
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
     * Updates option.
     * @param string $key
     * @param mixed $value
     * @return void
     * @since 1.0.0
     */
    public static function update_option( $key, $value ) {
        $options = self::get_options();
        $options[$key] = $value;

        update_option( self::SETTINGS_KEY, $options );
    }

    /**
     * Initializes the plugin.
     * @return void
     * @since 1.0.0
     */
    public static function initialize() {
        register_activation_hook( ALP_FILE, [__CLASS__, 'activate'] );
        register_deactivation_hook( ALP_FILE, [__CLASS__, 'deactivate'] );
        register_uninstall_hook( ALP_FILE, [__CLASS__, 'uninstall'] );

        add_action( 'init', [__CLASS__, 'init'] );
        add_action( 'admin_init', [__CLASS__, 'admin_init'] );
        add_action( 'admin_menu', [__CLASS__, 'admin_menu'] );
        add_action( 'plugins_loaded', [__CLASS__, 'plugins_loaded'] );
        add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts'] );
        add_action( 'admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts'] );

        //Hook our function , wi_create_backup(), into the action wi_create_daily_backup
        add_action( self::CRON_EVENT_KEY, [__CLASS__, 'lower_prices'] );
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
                __( 'Nastavení <em>CRON</em> skriptu' ),
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

        add_settings_field(
                'save_log',
                __( 'Uložit log', ALP_SLUG ),
                [__CLASS__, 'render_setting_save_log'],
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
        include( ALP_PATH . 'src/ALP_Listings_Screen.php' );
        include( ALP_PATH . 'src/ALP_Log_Screen.php' );

        /**
         * @var ALP_Options_Screen $options_screen
         */
        $options_screen = new ALP_Options_Screen();
        self::$admin_screens[$options_screen->get_slug()] = $options_screen;

        /**
         * @var ALP_Listings_Screen $listings_screen;
         */
        $listings_screen = new ALP_Listings_Screen();
        self::$admin_screens[$listings_screen->get_slug()] = $listings_screen;

        /**
         * @var ALP_Log_Screen $log_screen;
         */
        $log_screen = new ALP_Log_Screen();
        self::$admin_screens[$log_screen->get_slug()] = $log_screen;
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
     * @internal Renders setting `save_log`.
     * @return void
     * @since 1.0.0
     */
    public static function render_setting_save_log() {
        ob_start( function() {} );
        include( ALP_PATH . '/partials/setting-save_log.phtml' );
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
    public static function print_admin_notice( $msg, $type = 'info', $dismissible = true ) {
        $class = 'notice';

        if( in_array( $type, ['error','info','success','warning'] ) ) {
            $class .= ' notice-' . $type;
        } else {
            $class .= ' notice-info';
        }

        if( $dismissible === true) {
            $class .= ' is-dismissible';
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

    /**
     * Returns array describing next scheduled event:
     * <pre>
     * ['next_scheduled' => '11:00', 'hours_left' => 1]
     * </pre>
     * @return array
     * @since 1.0.0
     */
    public static function get_next_scheduled() {
        // Reset wpcron script if needed
        $next = wp_next_scheduled( ALP_Plugin::CRON_EVENT_KEY );
        if( $next === false ) {
            self::reset_wpcron_scheduled();
        }

        // Gather required data
        $next_his = date( 'H:i', $next );
        $next_txt = date( 'Y-m-d' ) . " {$next_his}:00";
        $next_obj = new DateTime( $next_txt );
        $is_tomorrow = false;

        // Add day if needed
        if( time() > strtotime( $next_txt ) ) {
            $is_tomorrow = true;
            $next_obj->add( new DateInterval( 'P1D' ) );
        }

        // Calculate hours left
        $hours_left = $next_obj->diff( new DateTime )->format( '%h' );

        // Return data
        return [
            'next_scheduled' => $next_his,
            // TODO Should be localized!
            'next_scheduled_full' => $next_obj->format( 'j.n.Y \v H:i' ),
            'hours_left' => $hours_left,
            'is_next_tomorrow' => $is_tomorrow,
        ];
    }

    /**
     * @internal Resets next scheduled execution of {@see ALP_Plugin::lower_prices()}.
     * @param boolean $start_again (Optional.) Defaultly TRUE.
     * @return void
     * @since 1.0.0
     */
    private static function reset_wpcron_scheduled( $start_again = true ) {
        $next_scheduled = self::get_option( 'execution_time' );
        $timestamp      = wp_next_scheduled( self::CRON_EVENT_KEY );
        $event_args     = [];

        // Remove old (if exists)
        if( $timestamp !== false ) {
            wp_unschedule_event( $timestamp, self::CRON_EVENT_KEY, $event_args );
        }

        // Set new
        if( $start_again !== false ) {
            $time = strtotime( $next_scheduled . ':00' );
            wp_schedule_event( $time, 'daily', self::CRON_EVENT_KEY, $event_args );
        }
    }

    /**
     * This is script executed to lower prices by the WP-Cron.
     * @see ALP_Plugin::initialize()
     * @see ALP_Plugin::initialize()
     * @return void
     * @since 1.0.0
     */
    public static function lower_prices() {
        // Get all acadp listings
        $data = [];
        $query = new WP_Query( [
            'nopaging'    => true,
            'post_type'   => 'acadp_listings', 
            'post_status' => 'publish',
            'meta_query'  => [
                [
                    'key'     => 'price',
                    'value'   => '',
                    'compare' => '!=',
                ],
                [
                    'key'     => 'price_orig',
                    'value'   => '',
                    'compare' => '!=',
                ],
                [
                    'key'     => 'price_reduce',
                    'value'   => '',
                    'compare' => '!=',
                ],
                [
                    'key'     => 'price_reduce_days',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ] );

        // There are now posts so no work to do
        if( ! $query->have_posts() ) {
            return $data;
        }

        // Go through all of them
        foreach( $query->get_posts() as $post ) {
            // Prepare helper object from the post (listing)
            $item = new ALP_Acadp_Items_Table_Item(
                $post->ID,
                $post->post_title,
                get_post_meta( $post->ID, 'price', true ),
                get_post_meta( $post->ID, 'price_reduce', true ),
                get_post_meta( $post->ID, 'price_reduce_days', true ),
                get_post_meta( $post->ID, 'price_orig', true )
            );

            $price_final = $item->get_price_final();

            // Check if is needed to lower price
            if( $item->price > $price_final ) {
                // If yes do it
                $per_day_reduce = $item->get_per_day_reduce();
                $_price_new = ceil( ( $item->price - $per_day_reduce ) );
                $price_new = ( $_price_new < $price_final ) ? $price_final : $_price_new;

                update_post_meta( $post->ID, 'price', $price_new );
            }
        }

        // Save date and time of last execution
        self::update_option( 'last_execution_time', date( 'Y-m-d H:i:s', time() ) );
    }
}

endif;
