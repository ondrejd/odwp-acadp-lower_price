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

if( ! class_exists( 'ALP_Log_DbTable' ) ) :

/**
 * Class for log database table.
 * @link https://developer.wordpress.org/reference/classes/wpdb/
 * @since 1.0.0
 */
class ALP_Log_DbTable {
    /**
     * @return string Returns name of the table.
     * @global wpdb $wpdb
     * @since 1.0.0
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . ALP_SLUG . '_log';
    }

    /**
     * @global wpdb $wpdb
     * @return string Returns size of data in MB of the table (e.g. "0.2 MB").
     * @since 1.0.0
     */
    public static function get_table_size() {
        global $wpdb;

        $table_name = self::get_table_name();
        $size = sprintf(
                '<span style="color: #f30;">%s</span>',
                __( 'nezjištěno', ALP_SLUG )
        );

        try {
            $ret = $wpdb->get_col(
                "SELECT round(((data_length + index_length) / 1024 / 1024), 2) " .
                "FROM information_schema.TABLES " .
                "WHERE " .
                "	table_schema = \"wordpress\" AND " .
                "   table_name = \"{$table_name}\"; "
            );

            if( is_array( $ret ) ) {
                if( count( $ret ) == 1 ) {
                    $size = $ret[0];
                }
            }
        } catch( Exception $e ) {
            // ...
        }

        return "{$size} MB";
    }

    /**
     * Creates table.
     * @global wpdb $wpdb
     * @return void
     * @since 1.0.0
     */
    public static function create_table() {
        global $wpdb;

        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
	                `log_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `post_id` BIGINT(20) NOT NULL,
                    `price_orig` INT(10) NOT NULL,
                    `price_new` INT(10) NOT NULL,
                    PRIMARY KEY (`log_id`)
                ) {$charset_collate};";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Inserts new log record.
     * @global wpdb $wpdb
     * @param \ALP_Log_Table_Item|array $item
     * @return array|integer Returns array of identifiers of new database items.
     * @since 1.0.0
     */
    public static function insert( $log ) {
        global $wpdb;

        $new_id = [];

        if( ( $log instanceof \ALP_Log_Table_Item ) ) {
            return self::insert_item( $log );
        }
        else if( is_array( $log ) ) {
            foreach( $log as $log_item ) {
                if( ( $log_item instanceof \ALP_Log_Table_Item ) ) {
                    $new_id[] = self::insert_item( $log_item );
                }
            }
        }

        return $new_id;
    }

    /**
     * @internal Inserts new log item into database.
     * @global wpdb $wpdb
     * @param \ALP_Log_Table_Item $item
     * @return integer Returns ID of the new log item.
     * @since 1.0.0
     */
    protected static function insert_item( \ALP_Log_Table_Item $item ) {
        global $wpdb;

        $wpdb->insert(
	        self::get_table_name(), [
		        'log_id'     => $item->get_log_id(),
		        'created'    => $item->get_created(),
		        'post_id'    => $item->get_post_id(),
		        'price_orig' => $item->get_price_orig(),
		        'price_new'  => $item->get_price_new(),
	        ], [ '%d', '%s', '%d', '%d', '%d' ]
        );

        return $wpdb->insert_id;
    }

    /**
     * Removes log record
     * @global wpdb $wpdb
     * @param array|integer $log Can be single ID or array of IDs.
     * @return boolean|integer Returns FALSE if SQL query went wrong or count of removed items.
     * @since 1.0.0
     */
    public static function remove( $log ) {
        global $wpdb;

        $table_name = self::get_table_name();
        $count = 0;
        $query = "DELETE FROM {$table_name} WHERE `log_id` = " . intval( $arr_str ) ." LIMIT 1 ";

        if( is_array( $log ) ) {
            $arr_str = implode( $log, ',' );
            $query = "DELETE FROM {$table_name} WHERE `log_id` IN ({$arr_str}) ";
        }

        try {
            $count = $wpdb->query( $query );
        } catch( Exception $e ) {
            return false;
        }

        return $count;
    }

    /**
     * Selects log records.
     * @global wpdb $wpdb
     * @return array Array of {@see ALP_Log_Table_Item}.
     * @since 1.0.0
     */
    public static function select_all() {
        global $wpdb;

        $table_name = self::get_table_name();
        $raw_results = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' ', ARRAY_A );
        $data = [];

        foreach( $raw_results as $item ) {
            $data[] = self::create_table_item_obj( $item );
        }

        return $data;
    }

    protected static function create_table_item_obj( array $item ) {
        return new ALP_Log_Table_Item(
            $item['log_id'],
            $item['created'],
            $item['post_id'],
            $item['price_orig'],
            $item['price_new']
        );
    }

    /**
     * Selects log record by its identifier.
     * @global wpdb $wpdb
     * @param integer $id
     * @return ALP_Log_Table_Item
     * @since 1.0.0
     */
    public static function select_by_id( $id ) {
        global $wpdb;

        $table_name = self::get_table_name();

        // ...

        return null;
    }

    /**
     * Updates log item.
     *
     * Example of usages:
     *
     * <pre>
     * // Updates single item with given data
     * ALP_Log_DbTable::update( 1, ['price_orig', 1000.00] );
     * // Updates log items with IDs 1,3,12 with given data
     * ALP_Log_DbTable::update( [1,3,12], ['price_orig', 1000.00] );
     * // Where $item is instance of ALP_Log_Table_Item
     * ALP_Log_DbTable::update( [$item], ['price_orig', 1000.00] );
     * // Where $item(*) are instances of ALP_Log_Table_Item
     * ALP_Log_DbTable::update( [$item1,$item2], ['price_new', 1000.00] );
     * </pre>
     *
     * @global wpdb $wpdb
     * @param ALP_Log_Table_Item|array|integer $log
     * @param ALP_Log_Table_Item|array $data (Optional.)
     * @return integer Returns count of updated items.
     * @since 1.0.0
     */
    public static function update( $log, $data = null ) {
        global $wpdb;

        $table_name = self::get_table_name();
        $count = 0;

        // ...

        return 0;
    }
}

endif;

