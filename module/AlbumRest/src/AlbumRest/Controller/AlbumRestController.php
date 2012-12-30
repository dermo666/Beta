<?php
/**
 * Album Rest Controller. 
 * 
 * @author dermisek
 */

namespace AlbumRest\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
 
use AlbumRest\Entity\Album;
//use Album\Form\AlbumForm;
//use Album\Model\AlbumTable;
use Doctrine\ODM\MongoDB\DocumentManager;
 
class AlbumRestController extends AbstractRestfulController
{
  
    /**
     * @var   Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;
    
    public function getList()
    {
        return new \Zend\View\Model\JsonModel(
                      array(
                        'data' => $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->findAll() 
                      )
                   );
    }
 
    public function get($id)
    {
        return new \Zend\View\Model\JsonModel(
                      array(
                          'data' => $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->findById($id) 
                      )
                   );
    }
 
    public function create($data)
    {
        
    }
 
    public function update($id, $data)
    {
        # code...
    }
 
    public function delete($id)
    {
        # code...
    }
    
    public function setDocumentManager(DocumentManager $dm)
    {
      $this->dm = $dm;
    }
    
    public function getDocumentManager()
    {
      if (null === $this->dm) {
        $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
      }
      return $this->dm;
    }
}