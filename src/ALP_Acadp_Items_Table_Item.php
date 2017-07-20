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

if( ! class_exists( 'ALP_Acadp_Items_Table_Item' ) ) :

/**
 * Log record.
 * @since 1.0.0
 */
class ALP_Acadp_Items_Table_Item {

    /**
     * @var integer $id
     * @since 1.0.0
     */
    public $id;

    /**
     * @var string $title
     * @since 1.0.0
     */
    public $title;

    /**
     * @var integer $price
     * @since 1.0.0
     */
    public $price;

    /**
     * @var integer $price_reduce
     * @since 1.0.0
     */
    public $price_reduce;

    /**
     * @var integer $price_reduce_days
     * @since 1.0.0
     */
    public $price_reduce_days;

    /**
     * @var integer $price_orig
     * @since 1.0.0
     */
    public $price_orig;

    /**
     * Construct.
     * @param integer $id
     * @param string  $title
     * @param integer $price
     * @param integer $price_reduce
     * @param integer $price_reduce_days
     * @param integer $price_orig
     * @since 1.0.0
     */
    public function __construct( $id, $title, $price, $price_reduce, $price_reduce_days, $price_orig ) {
        $this->id                = (int) $id;
        $this->title             = $title;
        $this->price             = (int) $price;
        $this->price_reduce      = (int) $price_reduce;
        $this->price_reduce_days = (int) $price_reduce_days;
        $this->price_orig        = (int) $price_orig;
    }

    /**
     * @return integer Final price (after reduce).
     * @since 1.0.0
     */
    public function get_price_final() {
        return ( (int) $this->price_orig / 100 ) * ( 100 - (int) $this->price_reduce );
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
     * @return integer Amount of day price reduce.
     * @since 1.0.0
     */
    public function get_price_reduce_day() {
        return $this->get_price_diff_final() / (int) $this->price_reduce_days;
    }
}

endif;
