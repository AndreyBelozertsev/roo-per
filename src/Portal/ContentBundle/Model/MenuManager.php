<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Menu;

class MenuManager
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
    private function getMenuRepo()
    {
        return $this->em->getRepository('PortalContentBundle:Menu');
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
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findOneById($id)
    {
        return $this->getMenuRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function find($id)
    {
        if ($id) {
            return $this->getMenuRepo()->find($id);
        } else {
            return $this->getMenuRepo()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Menu[]
     */
    public function findAll()
    {
        return $this->getMenuRepo()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findOneBy($array)
    {
        return $this->getMenuRepo()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findBy($array)
    {
        return $this->getMenuRepo()->findBy($array);
    }
}
