<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/controllers/CartController.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Cart Controller
 *
 * Controller used to receive requests about
 * the shopping cart and to show it when needed.
 *
 * Most of the methods are simply url tools and
 * will show nothing, only update the state of the
 * shopping cart.
 *
 * This class is prepared to send back a JSON
 * allowing the client side script to update the
 * shopping cart showed to the customer.
 *
 * @uses Home24_Controller_Action
 */
class CartController extends Home24_Controller_Action
{

    /**
     * Cart object for this session
     * @var Home24_Cart
     */
    protected $_cart = null;

    /**
     * init method, called by the __constructor
     *
     * Here is used to load a instance of
     * Home24_Cart
     *
     * @uses Home24_Cart
     * @return void
     */
    public function init()
    {
        $this->_cart = Home24_Cart::getInstance();
    }

    /**
     * Action Index (default action)
     *
     * Only exposes JSON data of the cart.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_helper->json->sendJson($this->_cart->toArray());
    }

    /**
     * Action Add
     *
     * To add products in the shopping cart.
     * This action is called by the default route
     * /cart/add/id/:id[/qty/:qty].
     *
     * If no Product ID is passed in the params or
     * if it is invalid, will redirect to the homepage.
     * 
     * If no quantity is passed, 1 is assumed.
     *
     * Tries to detect if this request is an AJAX and,
     * in this case, returns cart's updated JSON.
     * Otherwise, redirects to /cart/show
     *
     * @return void
     */
    public function addAction()
    {

        $productId = (int)$this->_getParam('id');

        if(!$productId) {
            $this->redirect('/');
        }

        $quantity = (int)$this->_getParam('qty');
        
        if(!$quantity) {
            $quantity = 1;
        }

        $this->_cart->add($productId, $quantity);
        
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {
            $this->_helper->json->sendJson($this->_cart->toArray());
        } else {
            $this->redirect('/cart/show');
        }
    }

    /**
     * Action Remove
     *
     * To remove products from the shopping cart.
     * This action is called by the default route
     * /cart/add/id/:id[/qty/:qty].
     *
     * If no Product ID is passed in the params or
     * if it is invalid, will redirect to the homepage.
     *
     * If no quantity is passed, 'all' is assumed.
     *
     * Tries to detect if this request is an AJAX and,
     * in this case, returns cart's updated JSON.
     * Otherwise, redirects to /cart/show
     *
     * @return void
     */
    public function removeAction()
    {
        $productId = (int)$this->_getParam('id');

        if(!$productId) {
            $this->redirect('/');
        }

        $quantity = (int)$this->_getParam('qty');
        
        if(!$quantity) {
            $quantity = 'all';
        }

        $this->_cart->remove($productId, $quantity);
        
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {
            $this->_helper->json->sendJson($this->_cart->toArray());
        } else {
            $this->redirect('/cart/show');
        }
    }

    /**
     * Action Empty
     *
     * To remove all products from the shopping cart.
     * This action is called by the default route
     * /cart/empty.
     *
     * Tries to detect if this request is an AJAX and,
     * in this case, returns cart's updated JSON.
     * Otherwise, redirects to /cart/show
     *
     * @return void
     */
    public function emptyAction()
    {

        $this->_cart->reset();
        
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {
            $this->_helper->json->sendJson($this->_cart->toArray());
        } else {
            $this->redirect('/cart/show');
        }
    }

    /**
     * Action Show
     *
     * Renders a page to show the shopping cart.
     * This action is called by the default route
     * /cart/show.
     *
     * @return void
     */
    public function showAction()
    {
        $this->loadModel('category');

        $this->view->headTitle()->prepend('Shopping Cart');

        $categories = $this->modelCategory->getAll();

        $this->view->categories = $categories;

        $this->view->cart = $this->_cart->toArray();

        $this->view->render('placeholder/header.phtml');

    }

    /**
     * Action Set
     *
     * Set the quantities in a lot.
     * This action is called by the default route
     * /cart/set, by POST request.
     *
     * The post request must cointain an array
     * named qty, where the keys are Product IDs
     * and the values are the quantities desired.
     *
     * Tries to detect if this request is an AJAX and,
     * in this case, returns cart's updated JSON.
     * Otherwise, redirects to /cart/show
     *
     * @return void
     */
    public function setAction()
    {
        $quantities = $this->getRequest()->getPost('qty');

        if(!$quantities) {
            $this->redirect('/');
        }

        $this->_cart->reset();

        foreach ($quantities as $productId => $quantity) {

            $this->_cart->add($productId, $quantity);

        }
        
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {
            $this->_helper->json->sendJson($this->_cart->toArray());
        } else {
            $this->redirect('/cart/show');
        }

    }


}











