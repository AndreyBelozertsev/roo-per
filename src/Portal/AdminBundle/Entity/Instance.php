<?php

namespace Portal\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instance
 *
 * @ORM\Table(name="instance")
 * @ORM\Entity(repositoryClass="Portal\AdminBundle\Repository\InstanceRepository")
 */
class Instance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=150, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=true)
     */
    private $domain;

    /**
     * @var Instance
     *
     * @ORM\ManyToOne(targetEntity="Portal\AdminBundle\Entity\Instance")
     * @ORM\JoinColumn(name="parent_instance_id", referencedColumnName="id")
     *
     * */
    private $parentInstance;

    /**
     * @var \Portal\ContentBundle\Entity\InstanceCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\InstanceCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *
     * */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Portal\UserBundle\Entity\RoleToUser", mappedBy="instance", cascade={"persist", "remove"},orphanRemoval=true)
     */
    protected $userRoles;

    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="string", length=255, nullable=true)
     */
    private $siteName;

    const PREFIX_DATABASE_DEFAULT = 'portal_';
    const MAIN_SITE_INSTANCE_CODE = 'main';

    public static $SKIP_MIGRATION_VERSION_LIST = array(
        '20170529150049',
        '20170809144609',
        '20170928152911',
        '20171108091805', // logs
        '20171108125506', // logs
        '20171109121017', // logs
        '20171121125242',
        '20171121131514',
        '20171122082902',
        '20171123112211',
        '20171124115229',
        '20171204094716',
        '20171204132906',
        '20171207085401',
        '20171207134430',
        '20180125080524',
        '20180126080811',
    );

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
     * @return Instance
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
     * Set title
     *
     * @param string $title
     *
     * @return Instance
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return Instance
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set parentInstance
     *
     * @param \Portal\AdminBundle\Entity\Instance $parentInstance
     *
     * @return Instance
     */
    public function setParentInstance($parentInstance)
    {
        $this->parentInstance = $parentInstance;

        return $this;
    }

    /**
     * Get parentInstance
     *
     * @return \Portal\AdminBundle\Entity\Instance
     */
    public function getParentInstance()
    {
        return $this->parentInstance;
    }

    /**
     * Set category
     *
     * @param \Portal\ContentBundle\Entity\InstanceCategory $category
     *
     * @return Instance
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Portal\ContentBundle\Entity\InstanceCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    public function __toString() {
        if ( isset($this->title) ) {
            return $this->title;
        } else {
            return '';
        }
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
     * @return Instance
     */
    public function setUserRoles($userRoles)
    {
        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * Set siteName
     *
     * @param string $siteName
     *
     * @return Instance
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }
}

