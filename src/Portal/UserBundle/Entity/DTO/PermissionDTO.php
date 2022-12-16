<?php

namespace Portal\UserBundle\Entity\DTO;

use Portal\UserBundle\Entity\Permission;

class PermissionDTO
{
    protected $id;
    protected $code;
    protected $label;
    protected $isSystem;

    public function __construct(Permission $permission)
    {
        $this->id = $permission->getId();
        $this->code = $permission->getCode();
        $this->label = $permission->getLabel();
        $this->isSystem = $permission->getIsSystem();
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
     * @return bool
     */
    public function getIsSystem()
    {
        return $this->isSystem;
    }
}