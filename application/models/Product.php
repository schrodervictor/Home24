<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/models/Product.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Product Model
 *
 * Holds the methods to communicate with the database
 * to retrive and save information about products
 *
 * @uses Home24_ModelAbstract
 * @uses Application_Model_DbTable_Product
 * @uses Application_Model_DbTable_Row_Product
 */
class Application_Model_Product extends Home24_ModelAbstract
{
     /**
     * Class name of the product DbTable
     * @var string
     */
    protected $_tableClass = 'Application_Model_DbTable_Product';

     /**
     * Class name of product DbTable_Row
     * @var string
     */
    protected $_rowClass = 'Application_Model_DbTable_Row_Product';

    /**
     * Method getCategoryToProduct (cacheable)
     * 
     * Gets the category for a certain product
     *
     * This method searchs in the table product_to_category
     *
     * @uses Aplication_Model_DbTable_ProductToCategory
     * @param string|integer $id The product ID
     * to search for categories
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getCategoryToProduct($id) {

        $p = $this->_createCacheToken(__FUNCTION__, func_get_args());
        $c = $this->_checkCache($p);
        if($c) return $c;

        $data = (new Application_Model_DbTable_ProductToCategory)
                ->fetchRow('product_id = ' . $id);

        $this->_saveInCache($p, $data);

        return $data;

    }

    /**
     * Method insert (refreshable)
     * 
     * Overrides original abstract method, because
     * the product also has to save it's category in
     * other table.
     * 
     * Inserts a new row in the tables.
     * Refreshes the cache state for the concrete Model.
     *
     * @uses Aplication_Model_DbTable_ProductToCategory
     * @param mixed[] $data Data to insert.
     * @return mixed The primary key value(s), as an associative array if the
     * key is compound, or a scalar if the key is single-column.
     */
    public function insert($data)
    {
        $id = $this->_table->createRow($data)->save();

        $p2cRow = (new Application_Model_DbTable_ProductToCategory)->createRow();

        $p2cRow->product_id = $id;
        $p2cRow->category_id = $data['category_id'];
        $result = $p2cRow->save();

        if($id || $result) $this->_refreshCacheState();

        return $id;
    }

    /**
     * Method update (refreshable)
     *
     * Overrides original abstract method, because
     * the product also has to save it's category in
     * other table.
     *
     * Updates the rows in the tables.
     * Refreshes the cache state for the concrete Model.
     *
     * @uses Aplication_Model_DbTable_ProductToCategory
     * @param mixed[] $data Data to insert.
     * @return mixed The primary key value(s), as an associative array if the
     * key is compound, or a scalar if the key is single-column.
     */
    public function update($data)
    {
        $row = $this->findById($data['id']);

        if(!$row->isConnected()) {
            $row->setTable($this->_table);
        }
        
        $id = $row->setFromArray($data)->save();

        $p2cRow = (new Application_Model_DbTable_ProductToCategory)->createRow();

        $p2cRow->product_id = $id;
        $p2cRow->category_id = $data['category_id'];
        $result = $p2cRow->save();

        if($id || $result) $this->_refreshCacheState();

        return $id;
    }

    /**
     * Method update (always refresh)
     *
     * Special method to check the database without
     * visitins the cache.
     *
     * @param mixed $id to search for.
     * @return Application_Model_DbTable_Row_Product
     */

    public function findByIdWithoutCache($id)
    {

        $row = $this->_table->fetchRow('id = ' . $id);

        return $row;

    }

}
