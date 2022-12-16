<?php

namespace Portal\UserBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Portal\UserBundle\Entity\DTO\RoleDTO;
use Portal\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserRoleManager
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
    private function getUserRoleRepo()
    {
        return $this->em->getRepository('PortalUserBundle:Role');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUserRoleToPermissionRepo()
    {
        return $this->em->getRepository('PortalUserBundle:RoleToPermission');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUserRoleToUserRepo()
    {
        return $this->em->getRepository('PortalUserBundle:RoleToUser');
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
        return $this->getUserRoleRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function find($id)
    {
        if ($id) {
            return $this->getUserRoleRepo()->find($id);
        } else {
            return $this->getUserRoleRepo()->findAll();
        }
    }

    /**
     * @return \Portal\UserBundle\Entity\User[]
     */
    public function findAll()
    {
        return $this->getUserRoleRepo()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneBy($array)
    {
        return $this->getUserRoleRepo()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findBy($array)
    {
        return $this->getUserRoleRepo()->findBy($array);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getRoleList($params = [])
    {
        $list = $this->getUserRoleRepo()->getRoleList($params);

        // To DTO
        return array_map(function(Role $role) {
            return new RoleDTO($role);
        }, $list);
    }

    /**
     * Remove all permissions
     * @param int $roleId
     * @return mixed
     */
    public function unsetPermissions($roleId)
    {
        return $this->getUserRoleToPermissionRepo()->unsetPermissions($roleId);
    }

    /**
     * @param $roleId
     * @return mixed
     */
    public function getPermissionsByRoleId($roleId)
    {
        return $this->getUserRoleToPermissionRepo()->getPermissionsByRoleId($roleId);
    }

    /**
     * Remove all roles
     * @param int $workerId
     * @return mixed
     */
    public function unsetRoles($workerId)
    {
        return $this->getUserRoleToUserRepo()->unsetRoles($workerId);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneRoleToUserById($id)
    {
        return $this->getUserRoleToUserRepo()->findOneById($id);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function findRoleToUserBy($params)
    {
        return $this->getUserRoleToUserRepo()->findBy($params);
    }

    /**
     * @param integer $userId
     * @return mixed
     */
    public function findGrantedInstancesByUserId($userId)
    {
        return $this->getUserRoleToUserRepo()->findGrantedInstancesByUserId($userId);
    }
}
