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
    protected $id;

    /**
     * @var string $text
     * @since 1.0.0
     */
    protected $text;

    /**
     * Construct.
     * @param integer $id
     * @param integer $text
     * @since 1.0.0
     */
    public function __construct( $id, $text ) {
        $this->id   = $id;
        $this->text = $text;
    }

    /**
     * @return integer
     * @since 1.0.0
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param integer $id
     * @return void
     * @since 1.0.0
     */
    public function setId( $id ) {
        $this->id = $id;
    }

    /**
     * @param string $message
     * @return void
     * @since 1.0.0
     */
    public function setText( $text ) {
        $this->text = $text;
    }
}

endif;
