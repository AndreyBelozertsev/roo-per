<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocumentTagManager
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
    private function getDocTagRepo()
    {
        return $this->em->getRepository('PortalContentBundle:DocumentTag');
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
     * @return \Portal\ContentBundle\Entity\DocumentTag[]
     */
    public function findAll()
    {
        return $this->getDocTagRepo()->findAll();
    }

    /**
     * @param array $array
     *
     * @return object
     */
    public function findOneBy($array)
    {
        return $this->getDocTagRepo()->findOneBy($array);
    }
}
