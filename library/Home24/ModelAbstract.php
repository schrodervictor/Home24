<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: library/Home24/ModelAbstract.php
 *
 * @category   Home24
 * @package    Home24
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Custom Model abstract class for Home24 Applications
 *
 * This is the base model class that concrete models in
 * the application should extend.
 *
 * Provides an compreensive API to communicate with
 * the Database and implements intelligent caching
 * methods.
 *
 * @uses Zend_Controller_Front
 */
abstract class Home24_ModelAbstract
{
    /**
     * Class name of DbTable to be used
     * by the concrete Model
     * @var string
     */
    protected $_tableClass;

    /**
     * Class name of DbTable_Row to be used
     * by the concrete Model
     * @var string
     */   
    protected $_rowClass;

    /**
     * DbTable instance in use by the concrete Model
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Cache resource
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Cache key prefix used by the concrete Model
     * @var string
     */  
    protected $_cache_prefix;
    
    /**
     * Constructor method
     *
     * Instanciates the $_table, gets the cache resource
     * and sets the $_cache_prefix default, if none was
     * specified by the concrete Model.
     *
     * @return void
     */
    public function __construct()
    {

        $this->_table = new $this->_tableClass;
        $this->_getMemcached();

        if(null === $this->_cache_prefix) {
            $this->_cache_prefix = get_class() . '_';
        }

    }

    /**
     * Protected method _getMemcached
     *
     * Retrieves the Cache resource from the
     * aplication bootstrap
     *
     * @return void
     * @uses Zend_Controller_Front
     */
    protected function _getMemcached() {


        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

        if($bootstrap->hasResource('Memcached')) {
            $this->_cache = $bootstrap->getResource('Memcached');
        }

    }

    /**
     * Protected method _checkCache
     *
     * Helper method available for other methods
     * to check if a certain key stills valid in
     * cache and returns it's value.
     *
     * @return mixed|false
     */
    protected function _checkCache($p) {

        if(!$this->_cache->test($p)) {
            
            return false;

        } else {

            return $this->_cache->load($p);
        }

    }

    /**
     * Protected method _saveInCache
     *
     * Helper method available for other methods
     * to save a value under a certain key.
     *
     * @return void
     */
    protected function _saveInCache($p, $data) {
        
        $this->_cache->save($data, $p);

    }

    /**
     * Protected method _getModelStateKey
     *
     * The implementation of the cache in
     * this Abstract Model is sensitive to data
     * changes inside each concrete Model.
     *
     * To accomplish that, a data state key is
     * placed inside the cache under this
     * ModelStateKey.
     *
     * @return string
     */
    protected function _getModelStateKey() {

        return $this->_cache_prefix . '_state';
    }

    /**
     * Protected method _createCacheToken
     *
     * The implementation of the cache in
     * this Abstract Model is sensitive to data
     * changes inside each concrete Model.
     *
     * Each piece of data inside the cache needs
     * a unique key, state sensible. This method
     * returns a valid and sensible key.
     *
     * @param string $method The identifier for the method
     * that generates the data to be cached.
     * @param mixed[] $args The arguments provided to generate
     * the data to be cached.
     * @return string
     */
    protected function _createCacheToken($method, $args) {

        $modelStateKey = $this->_getModelStateKey();

        $presentState = $this->_cache->load($modelStateKey);

        if(!$presentState) {
            $presentState = $this->_refreshCacheState();
        }

        $token = $this->_cache_prefix . $method . '_'
               . md5(serialize($args)) . $presentState;

        return $token;

    }

    /**
     * Protected method _refreshCacheState
     *
     * Invalidate the current state key for the concret Model
     * and generates a new one.
     *
     * @return string The new state key (you probably don't need
     * it for anything)
     */
    protected function _refreshCacheState() {

        $modelStateKey = $this->_getModelStateKey();

        $refreshedStateToken = bin2hex(openssl_random_pseudo_bytes(16));

        $this->_cache->save($refreshedStateToken, $modelStateKey);

        return $refreshedStateToken;

    }

    /**
     * Method getTable
     *
     * Returns the TableDb instance
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Method getAdapter
     *
     * Returns the Db Adapter currently in use
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_table->getAdapter();
    }

    /**
     * Method findById (cacheable)
     *
     * To find a single record by it's id
     *
     * @param mixed $id The ID for the desired record
     * @return Zend_Db_Table_Row_Abstract
     */
    public function findById($id)
    {
        $p = $this->_createCacheToken(__FUNCTION__, func_get_args());
        $c = $this->_checkCache($p);
        if($c) return $c;

        $row = $this->_table->fetchRow('id = ' . $id);

        $this->_saveInCache($p, $row);

        return $row;

    }

    /**
     * Method findByValue (cacheable, indiretly)
     *
     * To find a single record by a pair
     * of column, value
     *
     * @param string $column The column name to search
     * @param mixed $value The value to match
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByValue($column, $value)
    {
        return $this->findByValues(array($column => $value));
    }

    /**
     * Method findByValues (cacheable)
     *
     * To find a single record by a array
     * of column => value pairs
     *
     * @param mixed[] $values The array with values pairs
     * to search for (column => value)
     * @param string|string[] $order Optional. The desired sort order
     * @param integer $limit Optional. Limites the results to
     * a certain amount. Zero for no limit. Default to 0.
     * @param integer $skip Optional. Skips the first X results.
     * Useful for pagination. Defaut to 0.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByValues($values, $order = '', $limit = 0, $skip = 0)
    {
        $p = $this->_createCacheToken(__FUNCTION__, func_get_args());
        $c = $this->_checkCache($p);
        if($c) return $c;

        $db = $this->getAdapter();

        $select = $this->_table->select();

        foreach ($values as $key => $value) {
            $select->where($db->quoteIdentifier($key) . ' = ?', $value);
        }

        $select->order($order)->limit($limit, $skip);

        $data = $select->query()->fetchAll();

        $this->_saveInCache($p, $data);

        return $data;
        
    }

    /**
     * Method findAll (cacheable)
     *
     * Returns all values for a certain table
     *
     * @param string|string[] $order Optional. The desired sort order
     * @param integer $limit Optional. Limites the results to
     * a certain amount. Zero for no limit. Default to 0.
     * @param integer $skip Optional. Skips the first X results.
     * Useful for pagination. Defaut to 0.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAll($order = '', $limit = 0, $skip = 0)
    {
        $p = $this->_createCacheToken(__FUNCTION__, func_get_args());
        $c = $this->_checkCache($p);
        if($c) return $c;

        $select = $this->_table->select()->order($order)->limit($limit, $skip);

        $data = $select->query()->fetchAll();

        $this->_saveInCache($p, $data);

        return $data;
    }

    /**
     * Method getAll (cacheable, indiretly)
     *
     * An alias for findAll method
     *
     * @param string|string[] $order Optional. The desired sort order
     * @param integer $limit Optional. Limites the results to
     * a certain amount. Zero for no limit. Default to 0.
     * @param integer $skip Optional. Skips the first X results.
     * Useful for pagination. Defaut to 0.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAll($order = '', $limit = 0, $skip = 0)
    {
        return $this->findAll($order, $limit, $skip);
    }

    /**
     * Method insert (refreshable)
     *
     * Inserts a new row in the table.
     * Refreshes the cache state for the concrete Model.
     *
     * @param mixed[] $data Data to insert.
     * @return mixed The primary key value(s), as an associative array if the
     * key is compound, or a scalar if the key is single-column.
     */
    public function insert($data)
    {
        $result = $this->_table->createRow($data)->save();

        if($result) $this->_refreshCacheState();

        return $result;
    }

    /**
     * Method update (refreshable)
     *
     * Updates a row in the table.
     * Refreshes the cache state for the concrete Model.
     *
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
        
        $result = $row->setFromArray($data)->save();
        
        if($result) $this->_refreshCacheState();

        return $result;
    }

}

