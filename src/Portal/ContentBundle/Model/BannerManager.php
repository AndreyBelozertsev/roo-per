<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Banner;

class BannerManager
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
    private function getBannerRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Banner');
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
     * @return \Portal\ContentBundle\Entity\Banner
     */
    public function findOneById($id)
    {
        return $this->getBannerRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Banner
     */
    public function find($id)
    {
        if ($id) {
            return $this->getBannerRepository()->find($id);
        } else {
            return $this->getBannerRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Banner[]
     */
    public function findAll()
    {
        return $this->getBannerRepository()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Banner
     */
    public function findOneBy($array)
    {
        return $this->getBannerRepository()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Banner
     */
    public function findBy($array)
    {
        return $this->getBannerRepository()->findBy($array);
    }

}
