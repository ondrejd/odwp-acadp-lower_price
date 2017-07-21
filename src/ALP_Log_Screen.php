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

if( ! class_exists( 'ALP_Log_Table' ) ) {
    require_once( ALP_PATH . 'src/ALP_Log_Table.php' );
}

if( ! class_exists( 'ALP_Log_Screen' ) ) :

/**
 * Administration screen for the log viewer.
 * @since 1.0.0
 */
class ALP_Log_Screen extends ALP_Screen_Prototype {
    /**
     * @var string
     * @since 1.0.0
     */
    const SLUG = ALP_SLUG . '-log';

    /**
     * Constructor.
     * @param WP_Screen $screen Optional.
     * @return void
     * @since 1.0.0
     */
    public function __construct( \WP_Screen $screen = null ) {
        // Main properties
        $this->slug = self::SLUG;
        $this->menu_title = __( 'Změny cen', ALP_SLUG );
        $this->page_title = __( 'Záznam změn cen inzerátů', ALP_SLUG );

        // Specify help tabs
        $this->help_tabs = [];

        // Specify help sidebars
        $this->help_sidebars = [];

        // Specify screen options
        $this->enable_screen_options = false;
        $this->options = [];

        // Finish screen constuction
        parent::__construct( $screen );
    }

    /**
     * Action for `admin_menu` hook.
     * @return void
     * @since 1.0.0
     */
    public function admin_menu() {
        $this->hookname = add_management_page(
                $this->page_title,
                $this->menu_title,
                'manage_options',
                self::SLUG,
                [$this, 'render']
        );

        add_action( 'load-' . $this->hookname, [$this, 'screen_load'] );
    }
}

endif;
