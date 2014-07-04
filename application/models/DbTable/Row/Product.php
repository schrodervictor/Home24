<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/models/DbTable/Row/Product.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Product DbTable Row Model
 *
 * Implements Row Data Gateway Pattern
 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.row.html
 *
 * This class extends Zend_Db_Table_Row_Abstract to allow
 * specific methods when accessing or altering data in a specific
 * row, making the data itself more intelligent and dynamic.
 *
 * @uses Zend_Db_Table_Row_Abstract
 */
class Application_Model_DbTable_Row_Product extends Zend_Db_Table_Row_Abstract
{
	/**
     * Class name of the product DbTable
     * @var string
     */
    protected $_tableClass = 'Application_Model_DbTable_Product';

    /**
     * Method _insert
     * 
     * Overrides the original empty _insert
     * method, to perform pre-insert tasks
     * @uses DateTime
     * @return void
     */
    protected function _insert() {
        $this->date_added = (new DateTime())->format(DateTime::W3C);
        $this->date_updated = $this->date_added;
    }

    /**
     * Method _update
     * 
     * Overrides the original empty _update
     * method, to perform pre-update tasks
     * @uses DateTime
     * @return void
     */
    protected function _update() {
        $this->date_updated = (new DateTime())->format(DateTime::W3C);
    }

}

