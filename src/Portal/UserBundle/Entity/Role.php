<?php

namespace Portal\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="user_role")
 * @ORM\Entity(repositoryClass="Portal\UserBundle\Entity\Repository\RoleRepository")
 */
class Role
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=150, nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * @ORM\OneToMany(targetEntity="Portal\UserBundle\Entity\RoleToPermission", mappedBy="role", cascade={"persist", "remove"},orphanRemoval=true)
     */
    protected $permissions;

    /**
     * @ORM\OneToMany(targetEntity="Portal\UserBundle\Entity\RoleToUser", mappedBy="role", cascade={"persist", "remove"},orphanRemoval=true)
     */
    protected $roleUsers;

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
     * Set code
     *
     * @param string $code
     *
     * @return Role
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Role
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param mixed $permissions
     * @return Role
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function __toString() {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getRoleUsers()
    {
        return $this->roleUsers;
    }

    /**
     * @param mixed $roleUsers
     * @return Role
     */
    public function setRoleUsers($roleUsers)
    {
        $this->roleUsers = $roleUsers;

        return $this;
    }
}

