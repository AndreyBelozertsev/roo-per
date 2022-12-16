<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InternetResourcesManager
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
    private function getInternetResourcesRepo()
    {
        return $this->em->getRepository('PortalContentBundle:InternetResources');
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
     * @return \Portal\ContentBundle\Entity\InternetResources
     */
    public function findOneById($id)
    {
        return $this->getInternetResourcesRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\InternetResources
     */
    public function find($id)
    {
        return $id ? $this->getInternetResourcesRepo()->find($id) : $this->getInternetResourcesRepo()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\InternetResources[]
     */
    public function findAll()
    {
        return $this->getInternetResourcesRepo()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\InternetResources
     */
    public function findOneBy($array)
    {
        return $this->getInternetResourcesRepo()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\InternetResources
     */
    public function findBy($array, $orderBy = null)
    {
        return $this->getInternetResourcesRepo()->findBy($array, $orderBy);
    }
}