<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/controllers/CategoryController.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Category Controller
 *
 * Controller used to show informations about
 * category of products.
 *
 * @uses Home24_Controller_Action
 */
class CategoryController extends Home24_Controller_Action
{

    public function init()
    {
    }

    /**
     * Action Index (default action)
     *
     * This action is called by the route
     * /category/:categoryId, so it has access
     * to a param with the categoryId.
     *
     * Shows a page with the category information
     * and the product list.
     *
     * If there is no categoryId or if it is invalid
     * will redirect to the main page.
     *
     * @uses Application_Model_Category via loadModel
     * @return void
     */
    public function indexAction()
    {
        $categoryId = (int)$this->_getParam('categoryId');

        if(!$categoryId) {

            $this->redirect('/');
        }

        $this->loadModel('category');

        $category = $this->modelCategory->findById($categoryId);

        if(!$category) {

            $this->redirect('/');
        }

        $this->view->category = $category;

        $this->view->headTitle()->prepend($category['name']);

        $this->view->products = $this->modelCategory->getProductsByCategoryId($categoryId);

        $categories = $this->modelCategory->getAll();

        $this->view->categories = $categories;

        $this->view->render('placeholder/header.phtml');

    }

    /**
     * Action Add
     *
     * This action is called by the route
     * /category/add/.
     *
     * Shows a form to add a new category.
     *
     * @uses Application_Form_Category
     * @uses Application_Model_Category via loadModel
     * @return void
     */
    public function addAction()
    {

        $this->view->headTitle()->prepend('Add Category');

        $this->_helper->viewRenderer->setRender('form');

        $request = $this->getRequest();

        $form = new Application_Form_Category();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $this->loadModel('category');
                $this->modelCategory->insert($form->getValues());

                $this->view->success = true;
                $this->view->categoryName = $form->getValues()['name'];
                $this->view->actionPerformed = 'added';
                $form->reset();
            }
        }
 
        $this->view->form = $form;
    }

    /**
     * Action Edit
     *
     * This action is called by the route
     * /category/edit/id/:categoryId.
     *
     * Shows a form to edit the category info.
     *
     * If there is no id or if it is invalid
     * will redirect to the main page.
     *
     * @uses Application_Form_Category
     * @uses Application_Model_Category via loadModel
     * @return void
     */
    public function editAction()
    {


        $request = $this->getRequest();

        $categoryId = $request->getParam('id');

        if(!$categoryId) {
            return $this->redirect('/');
        }

        $this->_helper->viewRenderer->setRender('form');

        $form = new Application_Form_Category();

        $this->loadModel('category');
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $this->modelCategory->update($form->getValues());

                $this->view->success = true;
                $this->view->categoryName = $form->getValues()['name'];
                $this->view->actionPerformed = 'updated';
                $form->reset();
            }
        } else {

            $category = $this->modelCategory->findById($categoryId);

            if(!$category) {
                return $this->redirect('/');
            }

            $this->view->headTitle()->prepend('Edit Category ' . $category['name']);
    
            $form->populate($category->toArray());
        
        }
 
        $this->view->form = $form;

    }


}





