<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: library/Home24/Controller/Action.php
 *
 * @category   Home24
 * @package    Home24
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */


/**
 * Custom Controller class for Home24 Applications
 *
 * Extends Zend_Controller_Action
 *
 * Adds funcionalities to facilitate access
 * to models and sessions and write less code.
 *
 * @uses Zend_Controller_Action
 */
class Home24_Controller_Action extends Zend_Controller_Action
{

    /**
     * Method loadModel
     *
     * Facilitate load models inside the controllers
     * simply calling:
     * $this->loadModel('model-name');
     *
     * It sets the Model object to be accessible
     * in the property:
     * $this->modelModelName
     *
     * @return Home24_ModelAbstract
     */
    protected function loadModel($modelName, $options = null)
    {

        $appNamespace = $this->getInvokeArg('bootstrap')
                             ->getApplication()
                             ->getOptions()["appnamespace"];

        $modelName = ucfirst($modelName);

        $className = $appNamespace . '_Model_' . $modelName;

        if(!class_exists($className)) {

            throw new Exception('Unable to load model: ' . $modelName);         

        }

        $model = new $className($options);

        $this->{'model' . $modelName} = $model;

        return $model;

    }

    /**
     * Method loadSession
     *
     * Facilitate the access to the session
     * namespace, by calling
     * $this->loadSession();
     *
     * If the namespace for the Home24 application
     * It sets the session object to be accessible
     * in the property:
     * $this->session
     *
     * Otherwise, only returns the object. 
     *
     * @param string $sessionNamespace Optional. Default to 'home24'
     * @return Zend_Session_Namespace
     */
    protected function loadSession($sessionNamespace = 'home24')
    {

        $bootstrap = $this->getInvokeArg('bootstrap');

        if('home24' === $sessionNamespace) {

            $this->session = $bootstrap->getResource('Home24Session');

            return $this->session;

        }

        $session = new Zend_Session_Namespace($sessionNamespace);

        return $session;

    }


}