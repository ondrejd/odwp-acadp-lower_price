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
     * Creates table.
     * @global wpdb $wpdb
     * @return void
     * @since 1.0.0
     */
    public static function create_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . '_' . ALP_SLUG . '_log';
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
     * @return array Returns array of identifiers of new database items.
     * @since 1.0.0
     */
    public static function insert( \ALP_Log_Table_Item $log ) {
        global $wpdb;

        $new_id = [];

        // ...

        return $new_id;
    }

    /**
     * Removes log record
     * @global wpdb $wpdb.
     * @param ALP_Log_Table_Item|array|integer $log Can be single {@see ALP_Log_Table_Item}, array of {@see ALP_Log_Table_Item}, array of integers (<code>log_id</code>) or just single <code>log_id</code>.
     * @return integer Returns count of removed items.
     * @since 1.0.0
     */
    public static function remove( $log ) {
        global $wpdb;

        $count = 0;

        // ...

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

        $data = [];

        // ...

        return $data;
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

        // ...
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
     * @global wpdb $wpdb
     * @param ALP_Log_Table_Item|array|integer $log
     * @param ALP_Log_Table_Item|array $data (Optional.)
     * @return integer Returns count of updated items.
     * @since 1.0.0
     */
    public static function update( $log, $data = null ) {
        global $wpdb;

        // ...
    }
}

endif;

