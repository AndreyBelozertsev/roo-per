<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HeadManager
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
    private function getHeadRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Head');
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
     * @return \Portal\ContentBundle\Entity\Head
     */
    public function findOneById($id)
    {
        return $this->getHeadRepository()->findOneById($id);
    }

    /**
     * @param $id
     *
     * @return array|null|object
     */
    public function find($id)
    {
        if ($id) {
            return $this->getHeadRepository()->find($id);
        } else {
            return $this->getHeadRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Head[]
     */
    public function findAll()
    {
        return $this->getHeadRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Head
     */
    public function findOneBy($array)
    {
        return $this->getHeadRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     *
     * @return \Portal\ContentBundle\Entity\Head
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getHeadRepository()->findBy($array, $orderBy, $limit, $offset);
    }
    

    /**
     * @param array $filterParam
     *
     * @return array
     */
    public function getAllHeadForPagination($filterParam)
    {
        return $this->getHeadRepository()->getAllHeadForPagination($filterParam);
    }

    /**
     * @return array
     */
    public function getHeadList()
    {
        return $this->getHeadRepository()->getHeadList();
    }

    /**
     * @param int $structureId
     * @return array
     */
    public function getStructureHeadList($structureId)
    {
        return $this->getHeadRepository()->getStructureHeadList($structureId);
    }

    /**
     * @param int $structureId
     * @return array
     */
    public function getAdminStructureHeadList($structureId)
    {
        return $this->getHeadRepository()->getAdminStructureHeadList($structureId);
    }

    /**
     * @param string $ids
     * @return mixed
     */
    public function setHeadSortOrder($ids)
    {
        return $this->getHeadRepository()->setHeadSortOrder($ids);
    }
}
