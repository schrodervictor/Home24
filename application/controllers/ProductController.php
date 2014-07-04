<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/controllers/ProductController.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Product Controller
 *
 * Controller used to show and manipulate
 * individual products.
 *
 * @uses Home24_Controller_Action
 */
class ProductController extends Home24_Controller_Action
{

    public function init()
    {
    }

    /**
     * Action Index (default action)
     *
     * This action is called by the route
     * /product/:productId, so it has access
     * to a param with the productId.
     *
     * Shows a page with the product information.
     *
     * If there is no productId or if it is invalid
     * will redirect to the main page.
     *
     * @uses Application_Model_Product via loadModel
     * @uses Application_Model_Category via loadModel
     * @return void
     */
    public function indexAction()
    {

        $productId = (int)$this->_getParam('productId');

        if(!$productId) {

            $this->redirect('/');
        }

        $this->loadModel('product');

        $product = $this->modelProduct->findById($productId);

        if(!$product) {

            $this->redirect('/');
        }

        $this->view->product = $product;
        
        $this->view->headTitle()->prepend($product['name']);

        $defaultKeywords = $this->view->keywords;

        $this->view->headMeta()->setName('keywords', $defaultKeywords
            . ' some specifc keywords for this product');

        $this->loadModel('category');

        $categories = $this->modelCategory->getAll();

        $this->view->categories = $categories;

        $this->view->render('placeholder/header.phtml');

    }

    /**
     * Action Add
     *
     * This action is called by the route
     * /product/add/.
     *
     * Shows a form to add a new product.
     *
     * @uses Application_Form_Product
     * @uses Application_Model_Product via loadModel
     * @return void
     */
    public function addAction()
    {

        $this->view->headTitle()->prepend('Add Product');

        $this->_helper->viewRenderer->setRender('form');

        $request = $this->getRequest();

        $form = new Application_Form_Product();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $this->loadModel('product');
                $this->modelProduct->insert($form->getValues());

                $this->view->success = true;
                $this->view->productName = $form->getValues()['name'];
                $this->view->actionPerformed = 'added';
                $form->reset();
            }
        }
 
        $this->view->form = $form;
    }

    /**
     * Action Add
     *
     * This action is called by the route
     * /product/edit/id/:id.
     *
     * Shows a form to edit the product info.
     *
     * If there is no id or if it is invalid
     * will redirect to the main page.
     *
     * @uses Application_Form_Product
     * @uses Application_Model_Product via loadModel
     * @return void
     */
    public function editAction()
    {
        $request = $this->getRequest();

        $productId = $request->getParam('id');

        if(!$productId) {
            $this->redirect('/');
        }

        $this->_helper->viewRenderer->setRender('form');

        $form = new Application_Form_Product();

        $this->loadModel('product');
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $this->modelProduct->update($form->getValues());

                $this->view->success = true;
                $this->view->productName = $form->getValues()['name'];
                $this->view->actionPerformed = 'updated';
                $form->reset();
            }
        } else {

            $product = $this->modelProduct->findById($productId);

            $this->view->headTitle()->prepend('Edit Product ' . $product['name']);

            $categoryToProduct = $this->modelProduct->getCategoryToProduct($productId);

            $form->populate($product->toArray());

            $form->populate($categoryToProduct->toArray());
        
        }

 
        $this->view->form = $form;

    }


}





