<?php

namespace Portal\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Portal\HelperBundle\Model\LoadsProperties;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

// *              @ORM\AttributeOverride(name="email", column=@ORM\Column(nullable=true)),
// *              @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, nullable=true, unique=false)),
// *              @ORM\AttributeOverride(name="username", column=@ORM\Column(type="string", name="username", length=255, nullable=true, unique=false)),
// *              @ORM\AttributeOverride(name="usernameCanonical", column=@ORM\Column(type="string", name="username_canonical", length=255, nullable=true, unique=false))
/**
 * User
 * @ORM\Table(name="portal_user")
 * @ORM\Entity(repositoryClass="\Portal\UserBundle\Entity\Repository\UserRepository")
 * @ORM\AttributeOverrides({
 *
 * })
 * @UniqueEntity("username")
 */
class User extends BaseUser
{
    use LoadsProperties;

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
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     */
    protected $middleName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="home_phone", type="string", length=255, nullable=true)
     */
    protected $homePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="address_registered", type="string", length=512, nullable=true)
     */
    protected $addressRegistered;

    /**
     * @var string
     *
     * @ORM\Column(name="address_actual", type="string", length=512, nullable=true)
     */
    protected $addressActual;

    /**
     * @ORM\OneToMany(targetEntity="Portal\UserBundle\Entity\RoleToUser", mappedBy="user", cascade={"persist", "remove"},orphanRemoval=true)
     */
    protected $userRoles;

    /**
     * @ORM\Column(name="esia_id", type="string", length=255, nullable=true)
     */
    private $esiaId;

    /**
     * @ORM\Column(name="esia_access_token", type="string", length=2000, nullable=true)
     */
    private $esiaAccessToken;

    /**
     * @ORM\Column(name="esia_refresh_token", type="string", length=64, nullable=true)
     */
    private $esiaRefreshToken;

    const USER_ROLE = "ROLE_USER";
    const OPERATOR_ROLE = "ROLE_OPERATOR";
    const ADMIN_ROLE = "ROLE_ADMIN";
    const SUPER_ADMIN_ROLE = "ROLE_SUPER_ADMIN";
    
    public static $USER_ROLES = array(
        'users_form.role_operator' => 'ROLE_OPERATOR',
        'users_form.role_admin' => 'ROLE_ADMIN',
    );
    
   
    /**
     * Buff for changing Passwd
     * 
     * @var string
     */
    public $newPassword;

    public function __construct()
    {
        parent::__construct();
        $this->userRoles = new ArrayCollection();
    }
    
    /**
     * Get firstName
     *
     * @return string
     */
    function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }
    
    /**
     * Get middleName
     *
     * @return string
     */
    function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return User
     */
    function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }
    
    /**
     * Get phone
     *
     * @return string
     */
    function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     *
     * @return User
     */
    function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * Get addressRegistered
     *
     * @return string
     */
    function getAddressRegistered()
    {
        return $this->addressRegistered;
    }

    /**
     * Set addressRegistered
     *
     * @param string $addressRegistered
     *
     * @return User
     */
    function setAddressRegistered($addressRegistered)
    {
        $this->addressRegistered = $addressRegistered;

        return $this;
    }

    /**
     * Get addressActual
     *
     * @return string
     */
    function getAddressActual()
    {
        return $this->addressActual;
    }

    /**
     * Set addressActual
     *
     * @param string $addressActual
     *
     * @return User
     */
    function setAddressActual($addressActual)
    {
        $this->addressActual = $addressActual;

        return $this;
    }

    public function getFullUserName()
    {
        return $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * @param mixed $userRoles
     * @return User
     */
    public function setUserRoles($userRoles)
    {
        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * @return string
     */
    public function getEsiaId()
    {
        return $this->esiaId;
    }

    /**
     * @param $esiaId
     * @return $this
     */
    public function setEsiaId($esiaId)
    {
        $this->esiaId = $esiaId;

        return $this;
    }

    /**
     * Set esiaAccessToken
     *
     * @param string $esiaAccessToken
     *
     * @return User
     */
    public function setEsiaAccessToken($esiaAccessToken)
    {
        $this->esiaAccessToken = $esiaAccessToken;

        return $this;
    }

    /**
     * Get esiaAccessToken
     *
     * @return string
     */
    public function getEsiaAccessToken()
    {
        return $this->esiaAccessToken;
    }

    /**
     * Set esiaRefreshToken
     *
     * @param string $esiaRefreshToken
     *
     * @return User
     */
    public function setEsiaRefreshToken($esiaRefreshToken)
    {
        $this->esiaRefreshToken = $esiaRefreshToken;

        return $this;
    }

    /**
     * Get esiaRefreshToken
     *
     * @return string
     */
    public function getEsiaRefreshToken()
    {
        return $this->esiaRefreshToken;
    }
}
