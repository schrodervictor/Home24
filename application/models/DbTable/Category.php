<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/models/DbTable/Category.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Category DbTable Model
 *
 * Implements Table Data Gateway Pattern
 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.html
 *
 * This class extends Zend_Db_Table_Abstract to allow
 * specific methods when saving or retrieving data
 * into/from category table.
 *
 * That places a additional layer between the Model
 * and the database, allowing future adaptations or
 * changes in the database without altering the model
 * (which would means to alter all the controllers).
 *
 * @uses Zend_Db_Table_Abstract
 * @uses Application_Model_DbTable_Row_Category
 */
class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract
{

    /**
     * Database table name
     * @var string
     */
    protected $_name = 'category';

    /**
     * Class name of category DbTable_Row
     * @var string
     */
    protected $_rowClass = 'Application_Model_DbTable_Row_Category';

}

