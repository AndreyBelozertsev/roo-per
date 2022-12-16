<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\InstanceCategory;

class InstanceCategoryManager
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
    private function getInstanceCategoryRepository()
    {
        return $this->em->getRepository('PortalContentBundle:InstanceCategory');
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
     * @return \Portal\ContentBundle\Entity\InstanceCategory
     */
    public function findOneById($id)
    {
        return $this->getInstanceCategoryRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\InstanceCategory
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInstanceCategoryRepository()->find($id);
        } else {
            return $this->getInstanceCategoryRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\InstanceCategory[]
     */
    public function findAll()
    {
        return $this->getInstanceCategoryRepository()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\InstanceCategory
     */
    public function findOneBy($array)
    {
        return $this->getInstanceCategoryRepository()->findOneBy($array);
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\InstanceCategory
     */
    public function findBy($array)
    {
        return $this->getInstanceCategoryRepository()->findBy($array);
    }

    public function getCategoryListWithArticle()
    {
        return $this->getInstanceCategoryRepository()->getCategoryListWithArticle();
    }

    /**
     *
     * @return array
     */
    public function getInstanceList()
    {
        return $this->getInstanceCategoryRepository()->getInstanceList();
    }
}
