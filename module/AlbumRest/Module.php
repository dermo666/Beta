<?php
/**
 * Album Module. 
 * 
 * @author dermisek
 */

namespace AlbumRest;
 
use Zend\ModuleManager\Feature;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    Feature\BootstrapListenerInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
 
    /**
     * Set JSON View Strategy to this module (based on __NAMESPACE__).
     * 
     * @param EventInterface $e Event.
     * 
     * @return void
     */
    public function onBootstrap(EventInterface $e)
    {
      $app = $e->getApplication();
      $em  = $app->getEventManager()->getSharedManager();
      $sm  = $app->getServiceManager();
    
      $em->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function($e) use ($sm) {
        $strategy = $sm->get('ViewJsonStrategy');
        $view     = $sm->get('ViewManager')->getView();
        $strategy->attach($view->getEventManager());
      });
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}