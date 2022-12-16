<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\PhotoReport;

class PhotoReportManager
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
    private function getPhotoReportRepository()
    {
        return $this->em->getRepository('PortalContentBundle:PhotoReport');
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
     * @return \Portal\ContentBundle\Entity\PhotoReport
     */
    public function findOneById($id)
    {
        return $this->getPhotoReportRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\PhotoReport
     */
    public function find($id)
    {
        if ($id) {
            return $this->getPhotoReportRepository()->find($id);
        } else {
            return $this->getPhotoReportRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\PhotoReport[]
     */
    public function findAll()
    {
        return $this->getPhotoReportRepository()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\PhotoReport
     */
    public function findOneBy($array)
    {
        return $this->getPhotoReportRepository()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     * @param integer $orderBy
     * @param integer $limit
     *
     * @return \Portal\ContentBundle\Entity\PhotoReport
     */
    public function findBy($array, $orderBy = null, $limit = null)
    {
        return $this->getPhotoReportRepository()->findBy($array, $orderBy, $limit);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getAttachmentListPhotoReportById($id)
    {
        return $this->getPhotoReportRepository()->getAttachmentListPhotoReportById($id);
    }

    /**
     * @param int|null $limit
     * @param int $offset
     *
     * @return array
     */
    public function getPhotoReportList(int $limit = null, int $offset = 0)
    {
        return $this->getPhotoReportRepository()->getPhotoReportList($limit, $offset);
    }

    /**
     *@param integer $offset
     *@param integer $structureId
     *
     * @return array
     */
    public function getStructurePhotoReportList($structureId, int $offset = 0)
    {
        return $this->getPhotoReportRepository()->getStructurePhotoReportList($structureId, $offset);
    }

    /**
     *
     * @return integer
     */
    public function getPhotoReportListCount()
    {
        return $this->getPhotoReportRepository()->getPhotoReportListCount();
    }

    /**
     *@param integer $structureId
     *
     * @return integer
     */
    public function getStructurePhotoReportListCount($structureId)
    {
        return $this->getPhotoReportRepository()->getStructurePhotoReportListCount($structureId);
    }

    /**
     *
     * @return array
     */
    public function getAllPhotoReport()
    {
        return $this->getPhotoReportRepository()->getAllPhotoReport();
    }

    /**
     * @param integer $minId
     * @return array
     */
    public function getAllPhotoReportForSearchGrab($minId = 0)
    {
        return $this->getPhotoReportRepository()->getAllPhotoReportForSearchGrab($minId);
    }
    
    /**
     * @param integer $maxId
     * @return array
     */
    public function getAllPhotoReportForSearchUpdate($maxId = 0)
    {
        return $this->getPhotoReportRepository()->getAllPhotoReportForSearchUpdate($maxId);
    }
    
    /**
     * @param array $indexedPhotoReportsId
     * @return bool
     */
    public function updateIsSearchIndexedFlag($indexedPhotoReportsId)
    {
        return $this->getPhotoReportRepository()->updateIsSearchIndexedFlag($indexedPhotoReportsId);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getAttachmentListById($id)
    {
        return $this->getPhotoReportRepository()->getAttachmentListById($id);
    }
}
