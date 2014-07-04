<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/forms/Product.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Product Add/Edit Form
 *
 * @uses Zend_Form
 * @uses Application_Model_Category
 */
class Application_Form_Product extends Zend_Form
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
            'label'      => 'Product name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'alnum',
                    'options' => array('allowWhiteSpace' => true),
                )
            )
        ));

        $this->addElement('text', 'price', array(
            'label'      => 'Price:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Float'
            )
        ));

        $this->addElement('text', 'stock', array(
            'label'      => 'Stock:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Digits'
            )
        ));

        $this->addElement('text', 'dimensions', array(
            'label'      => 'Dimensions (HxWxD):',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'alnum'
            )
        ));

        $this->addElement('text', 'weight', array(
            'label'      => 'Weight (kg):',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Digits'
            )
        ));

        $this->addElement('text', 'image', array(
            'label'      => 'Main image:',
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));

        $categoryModel = new Application_Model_Category;

        $categories = $categoryModel->getAll();

        $categoriesRef = array();

        foreach ($categories as $category) {
        	$categoriesRef[$category['id']] = $category['name'];
        }

        $this->addElement('select', 'category_id', array(
            'label'        => 'Category:',
            'multiOptions' => $categoriesRef,
            'required'     => true,
            'validators'   => array(
                'Digits'
            )
        ));

        $this->addElement('textarea', 'description', array(
            'label'      => 'Category Description:',
            'required'   => true,
        ));

        $this->addElement('text', 'manufacturer_id', array(
            'label'      => 'Manufacturer id:',
            'required'   => false,
            'validators' => array(
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

