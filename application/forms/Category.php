<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/forms/Category.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Category Add/Edit Form
 *
 * @uses Zend_Form
 * @uses Application_Model_Category
 */
class Application_Form_Category extends Zend_Form
{

    public function init()
    {

        $this->setMethod('post');
 
        $this->addElement('hidden', 'id', array(
            'validators' => array(
                'Digits',
            )
        ));

        $this->addElement('text', 'name', array(
            'label'      => 'Category name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'alnum',
                    'options' => array('allowWhiteSpace' => true),
                )
            )
        ));
 
        $this->addElement('textarea', 'description', array(
            'label'      => 'Category Description:',
            'required'   => true,
        ));

        $categoryModel = new Application_Model_Category;

        $categories = $categoryModel->getAll();

        $categoriesRef = array();

        $categoriesRef[0] = 'None';

        foreach ($categories as $category) {
            $categoriesRef[$category['id']] = $category['name'];
        }

        $this->addElement('select', 'parent_category_id', array(
            'label'        => 'Parent Category:',
            'multiOptions' => $categoriesRef,
            'required'     => true,
            'validators'   => array(
                'Digits'
            )
        ));


        $this->addElement('radio', 'status', array(
            'label'        => 'Status:',
            'multiOptions' => array(
                0 => 'inactive',
                1 => 'active',
            ),
            'required'     => true,
            'validators'   => array(
                'Digits'
            )
        ));

        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));

    }


}

