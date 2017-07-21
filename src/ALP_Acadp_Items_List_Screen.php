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

if( ! class_exists( 'ALP_Acadp_Items_Table' ) ) {
    require_once( ALP_PATH . 'src/ALP_Acadp_Items_Table.php' );
}

if( ! class_exists( 'ALP_Acadp_Items_List_Screen' ) ) :

/**
 * Administration screen for the log viewer.
 * @since 1.0.0
 */
class ALP_Acadp_Items_List_Screen extends ALP_Screen_Prototype {
    /**
     * @var string
     * @since 1.0.0
     */
    const SLUG = ALP_SLUG . '-acadp_items';

    /**
     * Constructor.
     * @param WP_Screen $screen Optional.
     * @return void
     * @since 1.0.0
     */
    public function __construct( \WP_Screen $screen = null ) {
        // Main properties
        $this->slug = self::SLUG;
        $this->menu_title = __( 'Ceny inzerátů', ALP_SLUG );
        $this->page_title = __( 'Přehled cen inzerátů', ALP_SLUG );

        // Specify help tabs
        $this->help_tabs[] = [
            'id' => self::SLUG . '-helptab',
            'title' => __( 'Nápověda', DL_SLUG ),
            'content' => __( '<p>Na této stránce najdete přehled cen inzerátů, které byly vytvořeny pomocí pluginu <b>Advanced Classifieds & Directory Pro</b> a obsahují potřebné meta hodnoty <em>price</em>, <em>price_orig</em>, <em>price_reduce</em> a <em>price_reduce_days</em>.</p><p>Aktuální ceny jsou barevně zvýrazněny na základě jejich stavu:</p><ul><li><b style="color: blue;">modře</b> jsou zbarveny ceny inzerátů, které se ještě nezačali snižovat</li><li><b style="color: #f30;">červeným zbarvením</b> jsou zvýrazněny ceny inzerátů, která se již snižují, ale ještě nedosáhly minima</li><li><b style="color: green;">zeleně</b> jsou zvýrazněny ceny inzerátů, kde je již cena snížena na minimum</li></ul>', DL_SLUG ),
        ];

        // Specify help sidebars
        $this->help_sidebars[] = sprintf(
                __( '<b>Užitečné odkazy</b><ul><li><a href="%s" target="blank">Zdrojové kódy</a></li><li><a href="%s" target="blank">Advanced Classifieds &amp; Directory Pro</a></li></ul>', ALP_SLUG ),
                'https://github.com/ondrejd/odwp-acadp-lower_price',
                'https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/'
        );

        // Specify screen options
        $this->enable_screen_options = false;
        $this->options = [];

        // Finish screen constuction
        parent::__construct( $screen );

        // Check if user didn't press "Lower prices" button
        $lower_prices_btn = filter_input( INPUT_POST, 'do_lower_prices' );
        if( ! empty( $lower_prices_btn ) ) {
            ALP_Plugin::lower_prices();
        }
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
