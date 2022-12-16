<?php

namespace Portal\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleToPermission
 *
 * @ORM\Table(name="user_role2permission")
 * @ORM\Entity(repositoryClass="Portal\UserBundle\Entity\Repository\RoleToPermissionRepository")
 */
class RoleToPermission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Portal\UserBundle\Entity\Role", inversedBy="permissions")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *
     * */
    protected $role;

    /**
     * @var Permission
     *
     * @ORM\ManyToOne(targetEntity="Portal\UserBundle\Entity\Permission", inversedBy="roles")
     * @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     *
     * */
    protected $permission;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return RoleToPermission
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set permission
     *
     * @param Permission $permission
     *
     * @return RoleToPermission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}

