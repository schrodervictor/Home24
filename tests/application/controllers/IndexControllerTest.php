<?php

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testHomepageForRouteModuleControllerAndAction() {
    	$this->dispatch('/');
    	$this->assertRoute('default');
    	$this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testHomepageContentShouldHaveCategories() {
    	$this->dispatch('/');
    	$this->assertQuery('li.category');
    }
    
}