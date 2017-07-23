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

if( ! class_exists( 'ALP_Log_Table_Item' ) ) :

/**
 * Single item of table with log about price changes.
 * @see ALP_Log_DbTable
 * @since 1.0.0
 */
class ALP_Log_Table_Item {
    /**
     * @var integer $log_id
     * @since 1.0.0
     */
    protected $log_id;

    /**
     * @var string $created
     * @since 1.0.0
     */
    protected $created;

    /**
     * @var integer $post_id
     * @since 1.0.0
     */
    protected $post_id;

    /**
     * @var string $post_title
     * @since 1.0.0
     */
    private $post_title = '';

    /**
     * @var integer $price_orig
     * @since 1.0.0
     */
    protected $price_orig;

    /**
     * @var integer $price_new
     * @since 1.0.0
     */
    protected $price_new;

    /**
     * Constructor.
     * @param integer $log_id
     * @param string $created
     * @param integer $post_id
     * @param integer $price_orig
     * @param integer $price_new
     * @return void
     * @since 1.0.0
     */
    public function __construct( $log_id, $created, $post_id, $price_orig, $price_new ) {
        $this->log_id = (int) $log_id;
        $this->created = $created;
        $this->post_id = (int) $post_id;
        $this->price_orig = (int) $price_orig;
        $this->price_new = (int) $price_new;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_log_id() {
        return $this->log_id;
    }

    /**
     * @param boolean $formatted (Optional.) Defaultly FALSE.
     * @return string
     * @since 1.0.0
     */
    public function get_created( $formatted = false ) {
        return ( $formatted === true )
            ? ( new DateTime( $this->created ) )->format( 'j.n.Y H:i' )
            : $this->created;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_post_id() {
        return $this->post_id;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_post_title() {
        if( empty( $this->post_id ) ) {
            return '';
        }

        if( ! empty( $this->post_title ) ) {
            return $this->post_title;
        }

        $post = get_post( $this->get_post_id() );

        if( ( $post instanceof WP_Post ) ) {
            $this->post_title = $post->post_title;
        }

        return $this->post_title;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price_diff() {
        return $this->price_orig - $this->price_new;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price_new() {
        return $this->price_new;
    }

    /**
     * @return integer
     */
    public function get_price_orig() {
        return $this->price_orig;
    }

    /**
     * @param string $created
     * @return void
     * @since 1.0.0
     */
    public function set_created( $created ) {
        $this->created = $created;
    }

    /**
     * @param integer $log_id
     * @return void
     * @since 1.0.0
     */
    public function set_log_id( $log_id ) {
        $this->log_id = (int) $log_id;
    }

    /**
     * @param integer $post_id
     * @return void
     * @since 1.0.0
     */
    public function set_post_id( $post_id ) {
        $this->post_id = (int) $post_id;
    }

    /**
     * @param integer $price_new
     * @return void
     * @since 1.0.0
     */
    public function set_price_new( $price_new ) {
        $this->price_new = (int) $price_new;
    }

    /**
     * @param integer $price_orig
     * @return void
     * @since 1.0.0
     */
    public function set_price_orig( $price_orig ) {
        $this->price_orig = (int) $price_orig;
    }
}

endif;

