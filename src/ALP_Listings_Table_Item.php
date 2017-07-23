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

if( ! class_exists( 'ALP_Listings_Table_Item' ) ) :

/**
 * Log record.
 * @since 1.0.0
 * @todo Refactor - properties should be protected all accessed only through "get_*" and "set_*" methods.
 */
class ALP_Listings_Table_Item {

    /**
     * @var integer $id
     * @since 1.0.0
     */
    protected $id;

    /**
     * @var integer $price
     * @since 1.0.0
     */
    protected $price;

    /**
     * @var integer $price_orig
     * @since 1.0.0
     */
    protected $price_orig;

    /**
     * @var integer $price_reduce
     * @since 1.0.0
     */
    protected $price_reduce;

    /**
     * @var integer $price_reduce_days
     * @since 1.0.0
     */
    protected $price_reduce_days;

    /**
     * @var string $title
     * @since 1.0.0
     */
    protected $title;

    /**
     * Constructor.
     * @param integer $id
     * @param string  $title
     * @param integer $price
     * @param integer $price_reduce
     * @param integer $price_reduce_days
     * @param integer $price_orig
     * @since 1.0.0
     */
    public function __construct( $id, $title, $price, $price_orig, $price_reduce, $price_reduce_days ) {
        $this->id                = (int) $id;
        $this->title             = $title;
        $this->price             = (int) $price;
        $this->price_orig        = (int) $price_orig;
        $this->price_reduce      = (int) $price_reduce;
        $this->price_reduce_days = (int) $price_reduce_days;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return integer Amount of day price reduce.
     * @since 1.0.0
     */
    public function get_per_day_reduce() {
        return $this->get_price_diff_final() / (int) $this->price_reduce_days;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price() {
        return $this->price;
    }

    /**
     * @return integer Current price difference.
     * @since 1.0.0
     */
    public function get_price_diff() {
        return (int) $this->price_orig - (int) $this->price;
    }

    /**
     * @return integer Final price difference.
     * @since 1.0.0
     */
    public function get_price_diff_final() {
        return $this->price_orig - $this->get_price_final();
    }

    /**
     * @return integer Final price (after reduce).
     * @since 1.0.0
     */
    public function get_price_final() {
        return ( (int) $this->price_orig / 100 ) * ( 100 - (int) $this->price_reduce );
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price_orig() {
        return $this->price_orig;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price_reduce() {
        return $this->price_reduce;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function get_price_reduce_days() {
        return $this->price_reduce_days;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * @param integer $id
     * @return void
     * @since 1.0.0
     */
    public function set_id( $id ) {
        $this->id = (int) $id;
    }

    /**
     * @param integer $price
     * @return void
     * @since 1.0.0
     */
    public function set_price( $price ) {
        $this->price = (int) $price;
    }

    /**
     * @param integer $price_orig
     * @return void
     * @since 1.0.0
     */
    public function set_price_orig( $price_orig ) {
        $this->price_orig = (int) $price_orig;
    }

    /**
     * @param integer $price_reduce
     * @return void
     * @since 1.0.0
     */
    public function set_price_reduce( $price_reduce ) {
        $this->price_reduce = (int) $price_reduce;
    }

    /**
     * @param integer $price_reduce_days
     * @return void
     * @since 1.0.0
     */
    public function set_price_reduce_days( $price_reduce_days ) {
        $this->price_reduce_days = (int) $price_reduce_days;
    }

    /**
     * @param string $title
     * @return void
     * @since 1.0.0
     */
    public function set_title( $title ) {
        $this->title = $title;
    }
}

endif;
