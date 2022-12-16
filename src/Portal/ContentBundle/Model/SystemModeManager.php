<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\SystemMode;

class SystemModeManager
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
    private function getSystemModeRepository()
    {
        return $this->em->getRepository('PortalContentBundle:SystemMode');
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
     * @return \Portal\ContentBundle\Entity\SystemMode
     */
    public function findOneById($id)
    {
        return $this->getSystemModeRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\SystemMode
     */
    public function find($id)
    {
        if ($id) {
            return $this->getSystemModeRepository()->find($id);
        } else {
            return $this->getSystemModeRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\SystemMode[]
     */
    public function findAll()
    {
        return $this->getSystemModeRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\SystemMode
     */
    public function findOneBy($array)
    {
        return $this->getSystemModeRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @return \Portal\ContentBundle\Entity\SystemMode
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getSystemModeRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     *
     * @return array
     */
    public function getAllSystemMode()
    {
        return $this->getSystemModeRepository()->getAllSystemMode();
    }

    /**
     *
     * @return array
     */
    public function getSystemModeByCode($mode)
    {
        return $this->getSystemModeRepository()->getSystemModeByCode($mode);
    }
}
