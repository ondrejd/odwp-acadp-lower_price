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

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'ALP_Listings_Table_Item' ) ) {
    require_once( ALP_PATH . 'src/ALP_Listings_Table_Item.php' );
}

if( ! class_exists( 'ALP_Listings_Table' ) ) :

/**
 * Table with products created by "Advanced Classifieds & Directory Pro".
 * @since 1.0.0
 */
class ALP_Listings_Table extends WP_List_Table {

    /**
     * @var string Ascendant direction of the sorting.
     * @since 1.0.0
     */
    const DEFAULT_SORT_DIR_ASC  = 'asc';

    /**
     * @var string Descendant direction of the sorting.
     * @since 1.0.0
     */
    const DEFAULT_SORT_DIR_DESC = 'desc';

    /**
     * @var string Default per page items count.
     * @since 1.0.0
     */
    const DEFAULT_PER_PAGE = 25;

    /**
     * @var string Defaultly sorted column.
     * @since 1.0.0
     */
    const DEFAULT_SORT_COL = 'id';

    /**
     * @var string Default sorting direction.
     * @since 1.0.0
     */
    const DEFAULT_SORT_DIR = self::DEFAULT_SORT_DIR_DESC;

    /**
     * Constructor.
     * @param array $args (Optional.)
     * @return void
     * @since 1.0.0
     */
    public function __construct( $args = [] ) {
        parent::__construct( [
            'singular' => __( 'Inzerát', DL_SLUG ),
            'plural'   => __( 'Inzeráty', DL_SLUG ),
            'ajax'     => true,
        ] );
    }

    /**
     * Returns default options for the table.
     * @return array
     * @since 1.0.0
     */
    public static function get_default_options() {
        return [
            'per_page'    => self::DEFAULT_PER_PAGE,
            'sort_col'    => self::DEFAULT_SORT_COL,
            'sort_dir'    => self::DEFAULT_SORT_DIR,
        ];
    }

    /**
     * Returns options for the table.
     * @return array
     * @since 1.0.0
     */
    public static function get_options() {
        $user = get_current_user_id();

        $per_page = get_user_meta( $user, ALP_Listings_Screen::SLUG . '-per_page', true );
        if( strlen( $per_page ) == 0 ) {
            $per_page = self::DEFAULT_PER_PAGE;
        }

        $sort_col = get_user_meta( $user, ALP_Listings_Screen::SLUG . '-sort_col', true );
        if( strlen( $sort_col ) == 0 ) {
            $sort_col = self::DEFAULT_SORT_COL;
        }

        $sort_dir = get_user_meta( $user, ALP_Listings_Screen::SLUG . '-sort_dir', true );
        if( strlen( $sort_dir ) == 0 ) {
            $sort_dir = self::DEFAULT_SORT_DIR;
        }

        $defaults = self::get_default_options();
        $currents = [
            'per_page' => ( int ) $per_page,
            'sort_col' => $sort_col,
            'sort_dir' => $sort_dir,
        ];

        return array_merge( $defaults, $currents );
    }

    /**
     * Renders checkbox column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="acadp_item[]" value="%s">', $item->get_id()
        );
    }

    /**
     * @internal Renders contents of "id" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_id( ALP_Listings_Table_Item $item ) {
        return $item->get_id();
    }

    /**
     * @internal Renders contents of "title" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_title( ALP_Listings_Table_Item $item ) {
        return $item->get_title();
    }

    /**
     * @internal Renders contents of "price" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price( ALP_Listings_Table_Item $item ) {
        if( $item->get_price() == $item->get_price_orig() ) {
            $msg = __( '<span style="color: blue;">%s Kč</span>', ALP_SLUG );
        }
        else if( $item->get_price() > $item->get_price_final() ) {
            $msg = __( '<span style="color: #f30;">%s Kč</span>', ALP_SLUG );
        }
        else if( $item->get_price() == $item->get_price_final() ) {
            $msg = __( '<span style="color: green;">%s Kč</span>', ALP_SLUG );
        }

        return sprintf( $msg, $item->get_price() );
    }

    /**
     * @internal Renders contents of "price_diff_final" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_diff_final( ALP_Listings_Table_Item $item ) {
        $msg = __( '<span title="Hotové snížení ceny z cílového snížení ceny."><b>%s</b> Kč ze <b>%s</b> Kč</span>', ALP_SLUG );

        return sprintf( $msg, $item->get_price_diff(), $item->get_price_diff_final() );
    }

    /**
     * @internal Renders contents of "price_final" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_final( ALP_Listings_Table_Item $item ) {
        $msg = __( '%s Kč', ALP_SLUG );
        return sprintf( $msg, $item->get_price_final() );
    }

    /**
     * @internal Renders contents of "price_orig" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_orig( ALP_Listings_Table_Item $item ) {
        $msg = __( '%s Kč', ALP_SLUG );
        return sprintf( $msg, $item->get_price_orig() );
    }

    /**
     * @internal Renders contents of "price_reduce" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_reduce( ALP_Listings_Table_Item $item ) {
        return $item->get_price_reduce() . ' %';
    }

    /**
     * @internal Renders contents of "per_day_reduce" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_per_day_reduce( ALP_Listings_Table_Item $item ) {
        $msg = __( '%s Kč', ALP_SLUG );
        return sprintf( $msg, round( $item->get_per_day_reduce(), 0 ) );
    }

    /**
     * @internal Renders contents of "price_reduce_days" column.
     * @param ALP_Listings_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_reduce_days( ALP_Listings_Table_Item $item ) {
        $total_days     = (int) $item->get_price_reduce_days();
        $per_day_reduce = $item->get_per_day_reduce();
        $current_reduce = $item->get_price_orig() - $item->get_price();
        $current_days   = round( ( $current_reduce / $per_day_reduce ) );
        $message        = '<span title="Hotovo dnů z celkového počtu dnů."><b>%s</b> z <b>%s</b> dnů</span>';

        return sprintf( $message, $current_days, $total_days );
    }

    /**
     * Custom method for displaying rows.
     * @return void
     * @since 1.0.0
     */
    public function display_rows() {
        foreach( $this->items as $item ) {
            if( ! ( $item instanceof \ALP_Listings_Table_Item ) ) {
                continue;
            }

            $this->single_row( $item );
        }
    }

    /**
     * Returns array describing bulk actions available for the table.
     * @return array
     * @since 1.0.0
     */
    public function get_bulk_actions() {
        $actions = [];
        return $actions;
    }

    /**
     * Returns array with table columns.
     * @return array
     * @since 1.0.0
     */
    public function get_columns() {
        $columns = [
            'cb'                => '<input type="checkbox">',
            'id'                => __( 'ID', ALP_SLUG ),
            'title'             => __( 'Název', ALP_SLUG ),
            'price_orig'        => __( 'Původní cena', ALP_SLUG ),
            'price'             => __( 'Současná cena', ALP_SLUG ),
            'price_final'       => __( 'Konečná cena', ALP_SLUG ),
            'price_reduce'      => __( '<abbr title="Snížení v %">S</abbr>', ALP_SLUG ),
            'price_diff_final'  => __( 'Snížení (cena)', ALP_SLUG ),
            'price_reduce_days' => __( 'Snížení (dny)', ALP_SLUG ),
            'per_day_reduce'    => __( '<abbr title="Snížení za den v Kč">SZD</abbr>', ALP_SLUG ),
        ];
        return $columns;
    }

    /**
     * Returns array with table columns that can be hidden.
     * @return array
     * @since 1.0.0
     */
    public function get_hideable_columns() {
        $columns = [];
        return $columns;
    }

    /**
     * Returns array with table columns that are hidden.
     * @return array
     * @since 1.0.0
     */
    public function get_hidden_columns() {
        return [];
    }

    /**
     * Returns array with sortable table columns.
     * @return array
     * @since 1.0.0
     */
    public function get_sortable_columns() {
        $columns = [
            'id'                => ['id', false],
            'title'             => ['title', false],
            'price_orig'        => ['price_orig', false],
            'price'             => ['price', false],
            //'price_diff'        => ['price_diff', false],
            //'price_final'       => ['price_final', false],
            //'price_diff_final'  => ['price_diff_final', false],
            'price_reduce'      => ['price_reduce', false],
            //'price_reduce_days' => ['price_reduce_days', false],
            //'per_day_reduce'  => ['per_day_reduce', false],
        ];
        return $columns;
    }

    /**
     * Prepares data items for the table.
     * @return void
     * @since 1.0.0
     */
    public function prepare_items() {
        $options  = self::get_options();

        // Set up column headers
        $this->_column_headers = [
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns(),
        ];

        // Process bulk actions
        $this->process_bulk_actions();

        // Get order arguments
        extract( $this->get_order_args() );
        // Needed hack (because otherway is arrow indicating sorting
        // in table head not displayed correctly).
        $_GET['orderby'] = $orderby;
        $_GET['order'] = $order;

        // Prepare data
        $data_all = $this->get_data();
        $current  = 1;
        $per_page = $options['per_page'];

        // Apply sorting
        usort( $data_all, [$this, 'usort_reorder'] );

        // Pagination arguments
        $this->set_pagination_args( [
            'total_items' => count( $data_all ),
            'per_page'    => $per_page,
        ] );

        // Get data to display
        $data = array_slice( $data_all, ( ( $current - 1 ) * $per_page ), $per_page );
        $this->items = $data;
    }

    /**
     * @internal Returns array with sorting arguments ['orderby' => 'id', 'order' => 'asc'].
     * @return array
     * @since 1.0.0
     */
    private function get_order_args() {
        $options  = self::get_options();
        $orderby = filter_input( INPUT_POST, ALP_Listings_Screen::SLUG . '-sort_col' );
        $order = filter_input( INPUT_POST, ALP_Listings_Screen::SLUG . '-sort_dir' );

        if( empty( $orderby ) ) {
            $orderby = filter_input( INPUT_GET, 'orderby' );
        }

        if( empty( $orderby ) ) {
            $orderby = $options['sort_col'];
        }

        if( empty( $order ) ) {
            $order = filter_input( INPUT_GET, 'order' );
        }

        if( empty( $order ) ) {
            $order = $options['sort_dir'];
        }

        return ['order' => $order, 'orderby' => $orderby];
    }

    /**
     * Process bulk actions.
     * @return void
     * @since 1.0.0
     */
    public function process_bulk_actions() {
        // ...
    }

    /**
     * Get data.
     * @return array
     * @since 1.0.0
     */
    public function get_data() {
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

        if( ! $query->have_posts() ) {
            return $data;
        }

        foreach( $query->get_posts() as $post ) {
            $data[] = new ALP_Listings_Table_Item(
                    $post->ID,
                    $post->post_title,
                    get_post_meta( $post->ID, 'price', true ),
                    get_post_meta( $post->ID, 'price_orig', true ),
                    get_post_meta( $post->ID, 'price_reduce', true ),
                    get_post_meta( $post->ID, 'price_reduce_days', true )
            );
        }

        return $data;
    }

    /**
     * @internal Sorting method for the table data.
     * @param ALP_Listings_Table_Item $a The first row.
     * @param ALP_Listings_Table_Item $b The second row.
     * @return integer
     * @since 1.0.0
     */
    protected function usort_reorder( ALP_Listings_Table_Item $a, ALP_Listings_Table_Item $b ) {
        extract( $this->get_order_args() );
        $result = null;

        // numbers
        if( in_array( $orderby, ['id', 'price', 'price_reduce', 'price_reduce_days', 'price_orig'] ) ) {
            $method = "get_{$orderby}";
            $va = $a->{$method}();
            $vb = $b->{$method}();
            $result = ( $va < $vb ) ? -1 : ( ( $va > $vb ) ? 1 : 0 );
        }
        // strings
        else if( in_array( $orderby, ['title'] ) ) {
            $method = "get_{$orderby}";
            $result = strcmp( $a->{$method}(), $b->{$method}() );
        }

        return ( $order === 'asc' ) ? $result : -$result;
    }
}

endif;
