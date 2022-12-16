<?php

namespace Portal\AdminBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstanceManager
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
    private function getInstanceRepository()
    {
        return $this->em->getRepository('PortalAdminBundle:Instance');
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
     * @return \Portal\AdminBundle\Entity\Instance
     */
    public function findOneById($id)
    {
        return $this->getInstanceRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\AdminBundle\Entity\Instance
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInstanceRepository()->find($id);
        } else {
            return $this->getInstanceRepository()->findAll();
        }
    }

    /**
     * @return \Portal\AdminBundle\Entity\Instance[]
     */
    public function findAll()
    {
        return $this->getInstanceRepository()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\AdminBundle\Entity\Instance
     */
    public function findOneBy($array)
    {
        return $this->getInstanceRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     *
     * @return \Portal\AdminBundle\Entity\Instance
     */
    public function findBy($array)
    {
        return $this->getInstanceRepository()->findBy($array);
    }

    /**
     * @param array $instanceIds
     *
     * @return array
     */
    public function getInstanceDataByIds(array $instanceIds)
    {
        return $this->getInstanceRepository()->getInstanceDataByIds($instanceIds);
    }

    /**
     *
     * @return array
     */
    public function getInstanceList()
    {
        return $this->getInstanceRepository()->getInstanceList();
    }

    /**
     *
     * @return array
     */
    public function getInstanceCodeList()
    {
        return $this->getInstanceRepository()->getInstanceCodeList();
    }

    /**
     * Get pageTitle by instance code
     * @param string $instanceCode
     *
     * @return string
     */
    public function getPageTitle($instanceCode)
    {
        $instance = $this->getInstanceRepository()->findOneBy(['code' => $instanceCode]);

        return $instance->getSiteName() ?: $instance->getTitle();
    }
}
