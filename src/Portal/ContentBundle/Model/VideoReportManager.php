<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Portal\ContentBundle\Entity\VideoReport;

class VideoReportManager
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
    private function getVideoReportRepository()
    {
        return $this->em->getRepository('PortalContentBundle:VideoReport');
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
     * @return \Portal\ContentBundle\Entity\VideoReport
     */
    public function findOneById($id)
    {
        return $this->getVideoReportRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\VideoReport
     */
    public function find($id)
    {
        if ($id) {
            return $this->getVideoReportRepository()->find($id);
        } else {
            return $this->getVideoReportRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\VideoReport[]
     */
    public function findAll()
    {
        return $this->getVideoReportRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\VideoReport
     */
    public function findOneBy($array)
    {
        return $this->getVideoReportRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param integer $orderBy
     * @param integer $limit
     *
     * @return \Portal\ContentBundle\Entity\VideoReport
     */
    public function findBy($array, $orderBy = null, $limit = null)
    {
        return $this->getVideoReportRepository()->findBy($array, $orderBy, $limit);
    }

    /**
     * @param int|null $limit
     * @param int $offset
     *
     * @return array
     */
    public function getVideoReportList(int $limit = null, int $offset = 0)
    {
        return $this->getVideoReportRepository()->getVideoReportList($limit, $offset);
    }

    /**
     * @param integer $offset
     *
     * @return array
     */
    public function getStructureVideoReportList($structureId, int $offset = 0)
    {
        return $this->getVideoReportRepository()->getStructureVideoReportList($structureId, $offset);
    }

    /**
     *
     * @return integer
     */
    public function getVideoReportListCount()
    {
        return $this->getVideoReportRepository()->getVideoReportListCount();
    }

    /**
     *@param integer $structureId
     *
     * @return integer
     */
    public function getStructureVideoReportListCount($structureId)
    {
        return $this->getVideoReportRepository()->getStructureVideoReportListCount($structureId);
    }
}
