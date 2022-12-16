<?php

namespace Portal\UserBundle\Entity\DTO;

use Portal\UserBundle\Entity\User;

class UserDTO
{
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $middleName;
    protected $phone;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->middleName = $user->getMiddleName();
        $this->phone = $user->getPhone();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}