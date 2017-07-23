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

if( ! class_exists( 'ALP_Log_DbTable' ) ) {
    require_once( ALP_PATH . 'src/ALP_Log_DbTable.php' );
}

if( ! class_exists( 'ALP_Log_Table_Item' ) ) {
    require_once( ALP_PATH . 'src/ALP_Log_Table_Item.php' );
}

if( ! class_exists( 'ALP_Log_Table' ) ) :

/**
 * Table with log about price changes.
 * @see ALP_Log_DbTable
 * @see ALP_Log_Table_Item
 * @see ALP_Log_Screen
 * @since 1.0.0
 */
class ALP_Log_Table extends WP_List_Table {

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
    const DEFAULT_PER_PAGE = 50;

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
            'singular' => __( 'Záznam', DL_SLUG ),
            'plural'   => __( 'Záznamy', DL_SLUG ),
            'ajax'     => true,
        ] );
    }

    /**
     * Renders checkbox column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="log_id[]" value="%s">', $item->get_log_id()
        );
    }

    /**
     * @internal Renders contents of "log_id" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_log_id( ALP_Log_Table_Item $item ) {
        return $item->get_log_id();
    }

    /**
     * @internal Renders contents of "created" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_created( ALP_Log_Table_Item $item ) {
        return ( new DateTime( $item->get_created() ) )->format( 'j.n.Y H:i' );
    }

    /**
     * @internal Renders contents of "post_id" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_post_id( ALP_Log_Table_Item $item ) {
        return $item->get_post_id();
    }

    /**
     * @internal Renders contents of "post_title" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_post_title( ALP_Log_Table_Item $item ) {
        $html = '<span style="color: #f30;">---</a>';
        $post = get_post( $item->get_post_id() );

        if( ( $post instanceof WP_Post ) ) {
            $html = sprintf(
                    '<a href="%s">%s</a>',
                    admin_url( 'post.php?post=1&action=edit' ),
                    $post->post_title
            );
        }

        return $html;
    }

    /**
     * @internal Renders contents of "price_orig" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_orig( ALP_Log_Table_Item $item ) {
        $msg = __( '<span style="">%s Kč</span>', ALP_SLUG );

        return sprintf( $msg, $item->get_price_orig() );
    }

    /**
     * @internal Renders contents of "price_new" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_new( ALP_Log_Table_Item $item ) {
        $msg = __( '<span style="">%s Kč</span>', ALP_SLUG );

        return sprintf( $msg, $item->get_price_new() );
    }

    /**
     * @internal Renders contents of "price_diff" column.
     * @param ALP_Log_Table_Item $item
     * @return string
     * @since 1.0.0
     */
    public function column_price_diff( ALP_Log_Table_Item $item ) {
        $msg = __( '<span style="">%d Kč</span>', ALP_SLUG );
        $diff = $item->get_price_orig() - $item->get_price_new();

        return sprintf( $msg, $diff );
    }

    /**
     * Custom method for displaying rows.
     * @return void
     * @since 1.0.0
     */
    public function display_rows() {
        foreach( $this->items as $item ) {
            if( ! ( $item instanceof \ALP_Log_Table_Item ) ) {
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
        $actions = [
            'delete' => __( 'Smazat', ALP_SLUG ),
        ];
        return $actions;
    }

    /**
     * Returns array with table columns.
     * @return array
     * @since 1.0.0
     */
    public function get_columns() {
        $columns = [
            'cb'         => '<input type="checkbox">',
            'log_id'     => __( '<abbr title="ID záznamu">IDZ</abbr>', ALP_SLUG ),
            'post_id'    => __( '<abbr title="ID inzerátu">IDI</abbr>', ALP_SLUG ),
            'post_title' => __( 'Inzerát', ALP_SLUG ),
            'created'    => __( 'Vytvořeno', ALP_SLUG ),
            'price_orig' => __( 'Původní cena', ALP_SLUG ),
            'price_new'  => __( 'Nová cena', ALP_SLUG ),
            'price_diff'  => __( 'Rozdíl', ALP_SLUG ),
        ];
        return $columns;
    }

    /**
     * Get data.
     * @global wpdb $wpdb
     * @return array
     * @since 1.0.0
     */
    public function get_data() {
        return ALP_Log_DbTable::select_all();
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
     * Returns array with table columns that can be hidden.
     * @return array
     * @since 1.0.0
     */
    public function get_hideable_columns() {
        $columns = [];
        return $columns;
    }

    /**
     * Returns options for the table.
     * @return array
     * @since 1.0.0
     */
    public static function get_options() {
        $user = get_current_user_id();

        $per_page = get_user_meta( $user, ALP_Log_Screen::SLUG . '-per_page', true );
        if( strlen( $per_page ) == 0 ) {
            $per_page = self::DEFAULT_PER_PAGE;
        }

        $sort_col = get_user_meta( $user, ALP_Log_Screen::SLUG . '-sort_col', true );
        if( strlen( $sort_col ) == 0 ) {
            $sort_col = self::DEFAULT_SORT_COL;
        }

        $sort_dir = get_user_meta( $user, ALP_Log_Screen::SLUG . '-sort_dir', true );
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
     * @internal Returns array with sorting arguments ['orderby' => 'id', 'order' => 'asc'].
     * @return array
     * @since 1.0.0
     */
    private function get_order_args() {
        $options  = self::get_options();
        $orderby = filter_input( INPUT_POST, ALP_Log_Screen::SLUG . '-sort_col' );
        $order = filter_input( INPUT_POST, ALP_Log_Screen::SLUG . '-sort_dir' );

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
            'log_id'  => ['log_id', false],
            'post_id' => ['post_id', false],
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
     * Process bulk actions.
     * @return void
     * @since 1.0.0
     * @todo Check "_wpnonce"!
     */
    public function process_bulk_actions() {
        // Get action
        $action = filter_input( INPUT_POST, 'action' ); // action on top of the table
        if( empty( $action ) ) {
            $action = filter_input( INPUT_POST, 'action2' ); // action on bottom of the table
        }

        // No valid action selected, return
        if( ! in_array( $action, ['delete'] ) ) {
            return;
        }

        // Get selected log records
        $log_id = filter_input( INPUT_POST, 'log_id', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY );

        // No records selected, print admin notice and return
        if( empty( $log_id ) || !is_array( $log_id ) ) {
            ALP_Plugin::print_admin_notice( __( 'Nevybrali jste žádné záznamy k vymazání!', ALP_SLUG ), 'warning' );
            return;
        }

        // Remove selected records
        $ret = ALP_Log_DbTable::remove( $log_id );

        // Print output admin notice
        if( $ret === false ) {
            ALP_Plugin::print_admin_notice( __( 'Při mazání vybraných záznamů z databáze došlo k chybě!', ALP_SLUG ), 'error' );
        }
        else if( $ret == 0 ) {
            ALP_Plugin::print_admin_notice( __( 'Žádné záznamy z vybraných nebyly smazány.', ALP_SLUG ), 'info' );
        }
        else {
            ALP_Plugin::print_admin_notice( sprintf( __( 'Vybrané záznamy byly úpěšně smazány (počet smazaných položek: <b>%s</b>).', ALP_SLUG ), $ret ), 'success' );
        }
    }

    /**
     * @internal Sorting method for the table data.
     * @param ALP_Log_Table_Item $a The first row.
     * @param ALP_Log_Table_Item $b The second row.
     * @return integer
     * @since 1.0.0
     */
    protected function usort_reorder( ALP_Log_Table_Item $a, ALP_Log_Table_Item $b ) {
        extract( $this->get_order_args() );
        $val1 = null;
        $val2 = null;

        switch( $orderby ) {
            case 'log_id':
                $val1 = $a->get_log_id();
                $val2 = $b->get_log_id();
                break;

            case 'post_id':
                $val1 = $a->get_post_id();
                $val2 = $b->get_post_id();
                break;
        }

        $result = strcmp( $val1, $val2 );

        return ( $order === 'asc' ) ? $result : -$result;
    }
}

endif;
