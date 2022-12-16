<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocumentManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getDocumentRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Document');
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Document
     */
    public function find($id)
    {
        return $id ? $this->getDocumentRepository()->find($id) : $this->getDocumentRepository()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\Document[]
     */
    public function findAll()
    {
        return $this->getDocumentRepository()->findAll();
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Document
     */
    public function findOneById($id)
    {
        return $this->getDocumentRepository()->findOneById($id);
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Document
     */
    public function findOneBy($array)
    {
        return $this->getDocumentRepository()->findOneBy($array);
    }

    /**
     * @param $array
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function findBy($array, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getDocumentRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     * @param $page
     * @param null $lastTime
     * @return array
     */
    public function getMoreDocumentList($page = 0, $lastTime = null)
    {
        return $this->getDocumentRepository()->getMoreDocumentList($page, $lastTime);
    }

    /**
     * @param array $filterParam
     * @return array
     */
    public function getAllDocumentForPagination(array $filterParam)
    {
        return $this->getDocumentRepository()->getAllDocumentForPagination($filterParam);
    }

    /**
     * @param integer $minId
     * @return array
     */
    public function getAllDocumentsForSearchGrab($minId = 0)
    {
        return $this->getDocumentRepository()->getAllDocumentsForSearchGrab($minId);
    }
    
    /**
     * @param integer $maxId
     * @return array
     */
    public function getAllDocumentsForSearchUpdate($maxId = 0)
    {
        return $this->getDocumentRepository()->getAllDocumentsForSearchUpdate($maxId);
    }

    /**
     * @param integer $minId
     * @return array
     */
    public function getAllDocumentsForSearchDocumentGrab($minId = 0)
    {
        return $this->getDocumentRepository()->getAllDocumentsForSearchDocumentGrab($minId);
    }

    /**
     * @param integer $maxId
     * @return array
     */
    public function getAllDocumentsForSearchDocumentUpdate($maxId = 0)
    {
        return $this->getDocumentRepository()->getAllDocumentsForSearchDocumentUpdate($maxId);
    }
    
    /**
     * @param array $indexedDocumentsId
     * @return bool
     */
    public function updateDocumentsIsSearchIndexedFlag($indexedDocumentsId)
    {
        return $this->getDocumentRepository()->updateDocumentsIsSearchIndexedFlag($indexedDocumentsId);
    }
    
    /**
     * @param array $indexedDocumentsId
     * @return bool
     */
    public function updateDocumentsIsSearchDocumentIndexedFlag($indexedDocumentsId)
    {
        return $this->getDocumentRepository()->updateDocumentsIsSearchDocumentIndexedFlag($indexedDocumentsId);
    }

    /**
     *
     * @return array
     */
    public function getDocumentListCount()
    {
        return $this->getDocumentRepository()->getDocumentListCount();
    }

    /**
     * @param integer $structureId
     * @param integer $page
     *
     * @return array
     */
    public function getPublishedDocumentList($structureId, $page = null)
    {
        return $this->getDocumentRepository()->getPublishedDocumentList($structureId, $page);
    }

    /**
     * @param integer $structureId
     * @return integer|boolean
     */
    public function getPublishedDocumentCount($structureId)
    {
        return $this->getDocumentRepository()->getPublishedDocumentCount($structureId);
    }
}
