<?php
/**
 * Unit tests for Album Rest Controller.
 * 
 * @author dermisek
 */

namespace AlbumRestTest\Controller;
 
use AlbumRestTest\Bootstrap;
use AlbumRest\Controller\AlbumRestController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;
 
class AlbumRestControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
 
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new AlbumRestController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }
    
    public function shutDown()
    {
        
    }
 
    public function testGetListCanBeAccessed()
    {
        $repo = $this->getMock('Repository', array('findAll'));
        $repo->expects($this->once())->method('findAll')->will($this->returnValue(array('aaa'=>'aaa')));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository'), array(), '', FALSE);
        $dm->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
        
        $this->controller->setDocumentManager($dm);
        
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testGetCanBeAccessed()
    {
        $repo = $this->getMock('Repository', array('findById'));
        $repo->expects($this->once())->method('findById')->will($this->returnValue('bbb'));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository'), array(), '', FALSE);
        $dm->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

        $this->controller->setDocumentManager($dm);
        
        $this->routeMatch->setParam('id', '1');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testCreateCanBeAccessed()
    {
        $this->routeMatch->setParam('id', '1');
        $this->request->setMethod('post');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testUpdateCanBeAccessed()
    {
        $this->routeMatch->setParam('id', '1');
        $this->request->setMethod('put');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testDeleteCanBeAccessed()
    {
        $this->routeMatch->setParam('id', '1');
        $this->request->setMethod('delete');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
}