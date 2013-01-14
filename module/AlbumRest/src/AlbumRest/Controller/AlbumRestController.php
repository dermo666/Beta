<?php
/**
 * Album Rest Controller. 
 * 
 * @author dermisek
 */

namespace AlbumRest\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
 
use AlbumRest\Entity\Album;
use AlbumRest\Form\AlbumForm;
use Doctrine\ODM\MongoDB\DocumentManager;
 
class AlbumRestController extends AbstractRestfulController
{
  
    /**
     * @var   Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;
    
    public function getList()
    {
        $albums = $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->findAll();
        $data   = array();
        
        foreach ($albums as $album) {
            $data[] = $album->getArrayCopy();
        }
        
        return new \Zend\View\Model\JsonModel(
                      array(
                        'data' =>  $data
                      )
                   );
    }
 
    public function get($id)
    {
        $album = $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->find($id);
        
        return new \Zend\View\Model\JsonModel(
                      array(
                          'data' => $album->getArrayCopy()
                      )
                   );
    }
 
    public function create($data)
    {
        $form  = new AlbumForm();
        $album = new Album();
        
        $form->setInputFilter($album->getInputFilter());
        $form->setData($data);
        
        if ($form->isValid()) {
            $album->populate($form->getData());
            $this->getDocumentManager()->persist($album);
            $this->getDocumentManager()->flush();
            
            $id = $album->id;
        }
        
        return $this->get($id);
    }
 
    public function update($id, $data)
    {
        $data['id'] = $id;
        
        $album = $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->find($id);
        
        $form  = new AlbumForm();
        $form->bind($album);
        $form->setInputFilter($album->getInputFilter());
        $form->setData($data);
        
        if ($form->isValid()) {
            $this->getDocumentManager()->persist($album);
            $this->getDocumentManager()->flush();
        }
        
        return $this->get($id);
    }
 
    public function delete($id)
    {
        $album = $this->getDocumentManager()->getRepository('AlbumRest\Entity\Album')->find($id);
        
        $this->getDocumentManager()->remove($album);
        
        return new \Zend\View\Model\JsonModel(
                       array(
                           'data' => 'deleted'
                        )
        );
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