<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/models/Category.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Category Model
 *
 * Holds the methods to communicate with the database
 * to retrive and save information about categories
 *
 * @uses Home24_ModelAbstract
 * @uses Application_Model_DbTable_Category
 * @uses Application_Model_DbTable_Row_Category
 */
class Application_Model_Category extends Home24_ModelAbstract
{
    /**
     * Class name of the category DbTable
     * @var string
     */
    protected $_tableClass = 'Application_Model_DbTable_Category';

    /**
     * Class name of category DbTable_Row
     * @var string
     */
    protected $_rowClass = 'Application_Model_DbTable_Row_Category';

    /**
     * Method getProductsByCategoryId (cacheable)
     * 
     * Gets a rowset of products for a category
     *
     * This method returns a product rowset
     * given a categoryId.
     *
     * Optionally can be passed one or more ordering
     * filters, as so as a limit and skip parameters
     * to pagination
     *
     * The order parameter can be a string that matches
     * a column name for the table product, followed or
     * by the modifiers 'DESC' or 'ASC'. Can also be an
     * array of column names.
     *
     * @uses Application_Model_DbTable_Row_Product
     * @uses Zend_Db_Table_Rowset
     * @param string|integer $categoryId The category ID
     * to search for products
     * @param string|array $order Optional. Sorting order,
     * default to 'date_updated DESC'. If string, must match
     * a column name followed by a space and the keywords
     * 'ASC' or 'DESC'. If the keyword is omitted, 'ASC'
     * will be assumed. Can also be an array of such
     * strings
     * @param integer $limit Optional. Limites the quantity
     * of rows returned. Default to 10.
     * @param integer $skip Optional. Skips a certain amount
     * of results. Useful for pagination.
     * @return Zend_Db_Table_Rowset
     */
    public function getProductsByCategoryId($categoryId,
        $order = 'date_updated DESC', $limit = 10, $skip = 0)
    {
        $p = $this->_createCacheToken(__FUNCTION__, func_get_args());
        $c = $this->_checkCache($p);
        if($c) return $c;

        $db = $this->getAdapter();

        $select = $db->select()
                     ->distinct()
                     ->from(array('p' => 'product'))
                     ->join(array('p2c' => 'product_to_category'),
                            'p.id = p2c.product_id',
                            array())
                     ->join(array('c' => 'category'),
                            'c.id = p2c.category_id',
                            array())
                     ->where('c.id = ?', (int)$categoryId)
                     ->where('p.status = 1')
                     ->order($order)
                     ->limit($limit, $skip);

        $result = $select->query()->fetchAll();

        $rowSet = new Zend_Db_Table_Rowset(array(
            'rowClass' => 'Application_Model_DbTable_Row_Product',
            'data' => $result
        ));


        $this->_saveInCache($p, $rowSet);

        return $rowSet;

    }

}
