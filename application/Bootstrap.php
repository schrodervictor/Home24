<?php
/**
 * Victor Schröder's test task for Home24
 *
 * File: application/Bootstrap.php
 *
 * @category   Home24
 * @package    application
 * @author     Victor Schröder <schrodervictor@gmail.com>
 */

/**
 * Main bootstrap class to bootstrap the application's resources
 *
 * @uses Zend_Application_Bootstrap_Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Register Home24 custom library pseudonamespace
     *
     * Activates Zend's autoloader to know where
     * to search the custom classes for Home24 application
     *
     * The custom classes are located at:
     *
     *      library/Home24
     *
     * This library follows the same namming standards
     * of Zend library. For example:
     *
     *      Home24_Controller_Action class is located
     *      at library/Home24/Controller/Action.php
     *
     * Abstract classes and Interfaces follow a slightly
     * different pattern, as recommended by Zend:
     *
     *      Home_ModelAbstract abstract class is located
     *      at library/Home24/ModelAbstract.php
     *
     * @return boolean Allow to check success in registry
     */
    protected function _initHome24Library()
    {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Home24_');
        return true;
    }

    /**
     * Initialize Memcached resource application wide
     *
     * This is basically manipulated inside the
     * abstract class for models and in the
     * models themselves.
     *
     * A reference to this resource will be registred
     * under the key 'Memcached'.
     *
     * @return Zend_Cache_Core
     */
    protected function _initMemcached() {

        $frontendOptions = array(
            'caching' => true,
            'lifetime' => 3600,
            'cache_id_prefix' => 'Home24_',
            'automatic_serialization' => true
        );
          
        $backendOptions = array(
            'servers' =>array(
                array(
                'host' => 'localhost',
                'port' => 11211
                )
            ),
            'compression' => false
        );

        $memcached = Zend_Cache::factory('Core', 'Memcached',
            $frontendOptions, $backendOptions);

        return $memcached;
    }


    /**
     * Initialize session resource with custom key
     *
     * Initialize PHP session application wide
     * and sets a custom key 'home24' to store the values
     * to prevent conflicts with other modules, plugins or
     * components that may be also using sessions.
     *
     * A reference to this resource will be registred
     * in Zend_Registry under 'Home24Session'.
     *
     * @used-by Home24_Controller_Action
     * @return Zend_Session_Namespace
     */
    protected function _initHome24Session()
    {
        $session = new Zend_Session_Namespace('home24');
        return $session;
    }

    /**
     * Initialize routes
     *
     * The following routes are here initialized
     * for these controller/action pairs:
     *
     * /category/:categoryId
     *      \_ category/index
     *
     * /product/:productId
     *      \_ product/index
     *
     * And the default is still operating as normal:
     *
     * [/:module]/:controller/:action
     *      \_ module/product/action
     *
     * @uses Zend_Controller_Front
     * @uses Zend_Controller_Router_Route
     * @return Zend_Controller_Router_Interface
     */
    protected function _initRoutes()
    {

        $router = Zend_Controller_Front::getInstance()->getRouter();

        $route = new Zend_Controller_Router_Route(
            '/category/:categoryId',
            array(
                'module'     => 'default',
                'controller' => 'category',
                'action'     => 'index',
                'categoryId' => '0'
            ),
            array('categoryId' => '\d+')
        );

        $router->addRoute('category', $route);

        $route = new Zend_Controller_Router_Route(
            '/product/:productId',
            array(
                'module'     => 'default',
                'controller' => 'product',
                'action'     => 'index',
                'productId'  => '0'
            ),
            array('productId' => '\d+')
        );

        $router->addRoute('product', $route);

        return $router;

    }

    /**
     * Initialize default values for the head session
     *
     * This values can be overwritten in the controllers
     * or views to match specific needs.
     *
     * @return boolean Allow to check success in registry
     */
    protected function _initHeadDefaults()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');

        $view->headTitle('Home 24 by Victor Schröder')
             ->setSeparator(' | ');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $view->headMeta()->appendName('description', 'Möbel bequem von zu Hause aus bestellen ✓ Versandkostenfrei ✓ 30 Tage Rückgaberecht ✓ Verschiedene Zahlungsmethoden ✓ Mehr als 80.000 Artikel');
        $view->keywords = 'möbel online shop kaufen home24';
        $view->headMeta()->appendName('keywords', $view->keywords);
        $view->headMeta()->appendName('robots', 'index,follow');

        $view->headLink()->appendStylesheet('/stylesheets/index.css');

        return true;
    }

}

