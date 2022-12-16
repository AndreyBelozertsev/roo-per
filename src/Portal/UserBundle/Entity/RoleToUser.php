<?php

namespace Portal\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Portal\AdminBundle\Entity\Instance;

/**
 * RoleToUser
 *
 * @ORM\Table(name="user_role2user")
 * @ORM\Entity(repositoryClass="Portal\UserBundle\Entity\Repository\RoleToUserRepository")
 */
class RoleToUser
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Portal\UserBundle\Entity\User", inversedBy="userRoles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * */
    protected $user;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Portal\UserBundle\Entity\Role", inversedBy="roleUsers")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *
     * */
    protected $role;

    /**
     * @var Instance
     *
     * @ORM\ManyToOne(targetEntity="Portal\AdminBundle\Entity\Instance", inversedBy="userRoles")
     * @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     *
     * */
    protected $instance;


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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return RoleToUser
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return RoleToUser
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
     * Set instance
     *
     * @param Instance $instance
     *
     * @return RoleToUser
     */
    public function setInstance($instance)
    {
        $this->instance= $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }
}

