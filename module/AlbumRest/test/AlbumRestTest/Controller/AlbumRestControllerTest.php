<?php
/**
 * Unit tests for Album Rest Controller.
 * 
 * @author dermisek
 */

namespace AlbumRestTest\Controller;
 
use AlbumRest\Entity\Album;

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
 
    public function testGetListReturingListOfAlbums()
    {
        $album1 = new Album();
        $album1->populate(array('id' => 'aaa', 'artist' => 'Duran Duran', 'title' => 'View to a Kill'));
        $album2 = new Album();
        $album2->populate(array('id' => 'bbb', 'artist' => 'Thievery Corporation', 'title' => 'Culture of Fear'));
        
        $albums = array($album1, $album2);
        
        $repo = $this->getMock('Repository', array('findAll'));
        $repo->expects($this->once())->method('findAll')->will($this->returnValue($albums));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository'), array(), '', FALSE);
        $dm->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
        
        $this->controller->setDocumentManager($dm);
        
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testGetReturnsOneAlbum()
    {
        $album = new Album();
        $album->populate(array('id' => 'bbb', 'artist' => 'Thievery Corporation', 'title' => 'Culture of Fear'));
        
        $repo = $this->getMock('Repository', array('find'));
        $repo->expects($this->once())->method('find')->will($this->returnValue($album));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository'), array(), '', FALSE);
        $dm->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

        $this->controller->setDocumentManager($dm);
        
        $this->routeMatch->setParam('id', '1');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testCreateToAddNewAlbum()
    {
        $this->markTestIncomplete("Need factory to create the album");
        
        $this->routeMatch->setParam('data', array('id' => 'bbb', 'artist' => 'Thievery Corporation', 'title' => 'Culture of Fear'));
        $this->request->setMethod('post');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
 
    public function testUpdateToChangeTheAlbum()
    {
        $album = new Album();
        $album->populate(array('id' => 'aaa', 'artist' => 'Duran Duran', 'title' => 'View to a Kill'));
        
        $repo = $this->getMock('Repository', array('find'));
        $repo->expects($this->exactly(2))->method('find')->will($this->returnValue($album));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository', 'persist', 'flush'), array(), '', FALSE);
        $dm->expects($this->exactly(2))->method('getRepository')->will($this->returnValue($repo));
        $dm->expects($this->once())->method('persist')->with($this->equalTo($album));
        $dm->expects($this->once())->method('flush');
        
        $this->controller->setDocumentManager($dm);
        
        $this->routeMatch->setParam('id', 'bbb');
        $this->request->setMethod('put');
        $this->request->setContent('artist=Thievery Corporation&title=Culture of Fear');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Culture of Fear', $album->title);
    }
 
    public function testDeleteRemovesAlbum()
    {
        $album = new Album();
        
        $repo = $this->getMock('Repository', array('find'));
        $repo->expects($this->once())->method('find')->will($this->returnValue($album));
        
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('getRepository', 'remove'), array(), '', FALSE);
        $dm->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
        $dm->expects($this->once())->method('remove')->with($this->equalTo($album));
        
        $this->controller->setDocumentManager($dm);
        
        $this->routeMatch->setParam('id', '1');
        $this->request->setMethod('delete');
 
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
 
        $this->assertEquals(200, $response->getStatusCode());
    }
}