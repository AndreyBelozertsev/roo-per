<?php

namespace Portal\UserBundle\Entity\DTO;


use Portal\UserBundle\Entity\Role;
use Portal\UserBundle\Entity\RoleToPermission;

class RoleDTO
{
    protected $id;
    protected $code;
    protected $label;
    protected $permissions;
    protected $permissionIds;

    public function __construct(Role $role)
    {
        $this->id = $role->getId();
        $this->code = $role->getCode();
        $this->label = $role->getLabel();

        $permissions = [];
        $permissionIds = [];

        foreach ($role->getPermissions() as $roleToPermission) {
            $permissions[] = new PermissionDTO($roleToPermission->getPermission());
            $permissionIds[] = $roleToPermission->getPermission()->getId();
        }

        // Sort by Title
        usort($permissions, function($a, $b) {
            return ($a->getLabel() < $b->getLabel()) ? -1 : 1;
        });
        $this->permissions = $permissions;
        $this->permissionIds = $permissionIds;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function getPermissionIds()
    {
        return $this->permissionIds;
    }
}