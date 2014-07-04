<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/controllers/IndexController.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Index Controller
 *
 * Default Controller to show the main page.
 *
 * @uses Home24_Controller_Action
 */
class IndexController extends Home24_Controller_Action
{

    public function init()
    {
    }

    /**
     * Action Index (default action)
     *
     * Main page of the aplication.
     *
     * @uses Application_Model_Category via loadModel
     * @return void
     */
    public function indexAction()
    {
        $this->loadModel('category');

        $this->view->headTitle()->prepend('Homepage');

        $categories = $this->modelCategory->getAll();

        $this->view->categories = $categories;

        $this->view->render('placeholder/header.phtml');

        $this->view->render('placeholder/category-links.phtml');

        $this->view->render('placeholder/administrative-links.phtml');

    }


}

