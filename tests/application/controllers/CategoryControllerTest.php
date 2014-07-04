<?php

class CategoryControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testCategoryRouteModuleControllerAndAction() {
    	$this->dispatch('/category/2');
    	$this->assertRoute('category');
    	$this->assertModule('default');
        $this->assertController('category');
        $this->assertAction('index');

        $categoryId = $this->getRequest()->getParam('categoryId');
        $this->assertEquals('2', $categoryId);

        $this->resetRequest();

    	$this->dispatch('/category/add');
    	$this->assertRoute('default');
    	$this->assertModule('default');
        $this->assertController('category');
        $this->assertAction('add');

        $this->resetRequest();

    	$this->dispatch('/category/edit/id/1');
    	$this->assertRoute('default');
    	$this->assertModule('default');
        $this->assertController('category');
        $this->assertAction('edit');

        $categoryId = $this->getRequest()->getParam('id');
        $this->assertEquals('1', $categoryId);

    }

    public function testHomepageContentShouldHaveCategories() {
    	$this->dispatch('/');
    	$this->assertQuery('li.category');
    }

    public function testInvalidRequestsShouldRedirectToHomepage() {

    	$invalidId = '000';

    	$this->dispatch('/category');
    	$this->assertRedirectTo('/');

    	$this->resetRequest();
    	$this->dispatch('/category/' . $invalidId);
    	$this->assertRedirectTo('/');

	   	$this->resetRequest();
    	$this->dispatch('/category/edit/id/' . $invalidId);
    	$this->assertRedirectTo('/');

    }
    
}