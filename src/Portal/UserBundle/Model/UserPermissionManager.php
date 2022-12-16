<?php

namespace Portal\UserBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Portal\UserBundle\Entity\DTO\PermissionDTO;
use Portal\UserBundle\Entity\Permission;
use Portal\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserPermissionManager
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
    private function getUserPermissionRepo()
    {
        return $this->em->getRepository('PortalUserBundle:Permission');
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
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneById($id)
    {
        return $this->getUserPermissionRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function find($id)
    {
        if ($id) {
            return $this->getUserPermissionRepo()->find($id);
        } else {
            return $this->getUserPermissionRepo()->findAll();
        }
    }

    /**
     * @return \Portal\UserBundle\Entity\User[]
     */
    public function findAll()
    {
        return $this->getUserPermissionRepo()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneBy($array)
    {
        return $this->getUserPermissionRepo()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findBy($array)
    {
        return $this->getUserPermissionRepo()->findBy($array);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPermissionsList($params = [])
    {
        $list = $this->getUserPermissionRepo()->getPermissionsList($params);

        // To DTO
        return array_map(function(Permission $permission) {
            return new PermissionDTO($permission);
        }, $list);
    }
}
