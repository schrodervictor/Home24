<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: library/Home24/Cart.php
 *
 * @category   Home24
 * @package    Home24
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Custom Cart class for Home24 Applications
 *
 * The goal of this class is to act as a communication
 * layer between the controllers/views and the session,
 * where the cart's data resides.
 *
 * @uses Zend_Controller_Action
 * @uses Zend_Controller_Front
 * @uses Application_Model_Product
 * @uses Exception
 * @uses DateTime
 */
class Home24_Cart
{
    /**
     * Holds the single instance of this object
     * @var Home24_Cart
     */
    protected static $_instance;

    /**
     * The namespaced session
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Array of products currently in the cart
     * @var array
     */
    protected $_products = array();

    /**
     * The value of the products in the cart
     * @var float
     */
    protected $_total = 0;

    /**
     * The symbol for the currency to show
     * @var string
     */
    protected $_currency = 'EUR';

    /**
     * Timestamp for the last update for this
     * cart information. Useful for checking if
     * still valid.
     * @var DateTime
     */
    protected $_lastUpdated;

    /**
     * Constructor method
     *
     * Get the session namespace and read
     * cart information that is eventually there
     *
     * @return void
     */
    public function __construct() {

        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

        if(!$bootstrap->hasResource('Home24Session')) {
            throw new Exception('Home24Session not found. Sessions must be enabled...');
        }
        
        $this->_session = $bootstrap->getResource('Home24Session');

        $this->readFromSession();

    }

    /**
     * Method getInstance
     *
     * To enforce the existence of
     * only one Cart instance per session
     *
     * @return Home24_Cart
     */
    public static function getInstance() {

        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    /**
     * Method readFromSession
     *
     * Updates the instance with
     * the existing data in the session
     *
     * Returns this for chaining
     *
     * @return Home24_Cart
     */
    protected function readFromSession() {

        $cart = $this->_session->cart;

        $this->_products = $cart['products'] ? $cart['products'] : array();
        $this->_total = $cart['total'] ? $cart['total'] : 0;
        $this->_currency = $cart['currency'] ? $cart['currency'] : 'EUR';
        $this->_lastUpdated = $cart['lastUpdated'] ? $cart['lastUpdated'] : new DateTime();

        return $this;
    }

    /**
     * Method saveToSession
     *
     * Updates the session with
     * the informations about the cart
     *
     * Returns this for chaining
     *
     * @return Home24_Cart
     */
    protected function saveToSession() {

        $cart = $this->_session->cart;

        $cart['products'] = $this->_products;
        $cart['total'] = $this->_total;
        $cart['currency'] = $this->_currency;
        $cart['lastUpdated'] = $this->_lastUpdated;

        $this->_session->cart = $cart;

        return $this;
    }

    /**
     * Method reset
     *
     * Empty the shopping cart
     *
     * @return void
     */
    public function reset() {

        $this->_products = array();
        $this->_total = 0;
        $this->_currency = 'EUR';
        $this->_lastUpdated = new DateTime();

        $this->saveToSession();
    }

    /**
     * Method add
     *
     * Add a product to the shopping cart
     *
     * @param integer $id The product ID to add
     * @param integer $quantity Optional. Default to 1
     * @param array $options Optional. Not yet implementaded
     *
     * @return Home24_Cart
     */
    public function add($id, $quantity = 1, $options = array()) {

        $product['id'] = $id;

        if($this->_products[$id]) {
            $quantity += $this->_products[$id]['quantity'];
        }

        $product['quantity'] = $quantity;

        $this->_products[$id] = $product;

        return $this->update();

    }

    /**
     * Method remove
     *
     * Removes a product from the shopping cart
     *
     * @param integer $id The product ID to remove
     * @param integer|string $quantity Optional. Quantity to
     * deduct. The special flag 'all' will remove the product
     * from the cart. Default to 'all'.
     * @param array $options Optional. Not yet implementaded
     *
     * @return Home24_Cart
     */
    public function remove($id, $quantity = 'all', $options = array()) {

        if(!$this->_products[$id]) {
            return false;
        }

        $product = $this->_products[$id];

        $quantity = $quantity === 'all' ? $product['quantity'] : $quantity;

        $product['quantity'] -= $quantity;

        $this->_products[$id] = $product;

        return $this->update();

    }

    /**
     * Method update
     *
     * Updates the shopping cart with fresh information
     * from the database, to grant reliable info to the
     * customer.
     *
     * @return Home24_Cart
     */
    public function update() {

        $modelProduct = new Application_Model_Product();

        $total = 0;

        foreach ($this->_products as $id => $product) {

            if($product['quantity'] <= 0) {
                unset($this->_products[$id]);
                continue;
            }

            $productData = $modelProduct->findByIdWithoutCache($id);

            if(!$productData) {
                unset($this->_products[$id]);
                continue;
            }

            $product['id'] = $productData['id'];
            $product['name'] = $productData['name'];
            $product['price'] = $productData['price'];
            $product['dimensions'] = $productData['dimensions'];
            $product['weight'] = $productData['weight'];

            if($product['quantity'] > $productData['stock']) {
                $product['quantity'] = $productData['stock'];
            }

            $product['subtotal'] = $product['quantity'] * $product['price'];

            $total += $product['subtotal'];

            $this->_products[$id] = $product;

        }

        $this->_total = $total;

        $this->_lastUpdated = new DateTime();

        $this->saveToSession();

        return $this;

    }

    /**
     * Method toArray
     *
     * Provides the cart information in form of an
     * array. Useful for using in the views.
     *
     * @return array
     */
    public function toArray() {

        $a = array(
            'products'    => $this->_products,
            'total'       => $this->_total,
            'currency'    => $this->_currency,
            'lastUpdated' => $this->_lastUpdated->format(DateTime::W3C)
        );

        return $a;

    }

}