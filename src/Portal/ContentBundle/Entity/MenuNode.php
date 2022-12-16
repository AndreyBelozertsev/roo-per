<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MenuNode
 *
 * @ORM\Table(name="menu_node")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MenuNodeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MenuNode
{
    const PERMISSIONS_STRUCTURE = [
        'create' => 'create_structure',
        'edit' => 'edit_structure',
        'delete' => 'delete_structure',
        'restore' => 'restore_structure'
    ];

    const PERMISSIONS_MENU_NODE = [
        'create' => 'create_menu_node',
        'edit' => 'edit_menu_node',
        'delete' => 'delete_menu_node'
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"createdAt", "title"}, updatable=false, separator="_", unique=true)
     * @ORM\Column(name="slug", type="string", length=150, nullable=true)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var MenuNode
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MenuNode", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\MenuNode", mappedBy="parent")
     * @ORM\OrderBy({"order"="DESC", "createdAt"="DESC"})
     */
    protected $childs;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\Menu")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menu;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=500, nullable=true)
     */
    private $route;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\Article", mappedBy="menuNode")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\FeedbackForm", mappedBy="menuNode")
     */
    private $feedBacks;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\PhotoReport", mappedBy="menuNode")
     */
    private $photoReports;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\VideoReport", mappedBy="menuNode")
     */
    private $videoReports;

    /**
     * @var int
     *
     * @ORM\Column(name="node_order", type="integer", nullable=false, options={"default" = 0})
     */
    private $order = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="before_text", type="text", nullable=true)
     */
    private $beforeText;

    /**
     * @var string
     *
     * @ORM\Column(name="after_text", type="text", nullable=true)
     */
    private $afterText;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_separator", type="boolean", nullable=true, options={"default":0})
     */
    private $isSeparator = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_main", type="boolean", nullable=true, options={"default":0})
     */
    private $isMain = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hide_childs", type="boolean", nullable=true, options={"default":0})
     */
    private $isHideChilds = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=true, options={"default":0})
     */
    private $isHidden = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_link_on_id", type="boolean", nullable=true, options={"default":0})
     */
    private $isLinkOnId = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true, options={"default":1})
     */
    private $isPublished = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true, options={"default":0})
     */
    private $isDeleted = false;

    /**
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="manual_updated_at", type="datetime", nullable=true)
     */
    private $manualUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="manual_created_at", type="datetime", nullable=true)
     */
    private $manualCreatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_search_indexed", type="boolean", nullable=false, options={"default":0})
     */
    private $isSearchIndexed = false;

    public function __construct()
    {
        $this->photoReports = new ArrayCollection();
        $this->videoReports = new ArrayCollection();
        $this->feedBacks = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->childs = new ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    public function getArticles()
    {
        return $this->articles;
    }

    public function getPhotoReports()
    {
        return $this->photoReports;
    }

    public function getVideoReports()
    {
        return $this->videoReports;
    }

    public function getFeedBacks()
    {
        return $this->feedBacks;
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return MenuNode
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set parent
     *
     * @param \Portal\ContentBundle\Entity\MenuNode $parent
     *
     * @return MenuNode
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set childs
     *
     * @param \Portal\ContentBundle\Entity\MenuNode[] $childs
     *
     * @return MenuNode
     */
    public function setChilds($childs)
    {
        $this->childs = $childs;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set menu
     *
     * @param \Portal\ContentBundle\Entity\Menu $menu
     *
     * @return MenuNode
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return MenuNode
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return MenuNode
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return MenuNode
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @return MenuNode
     */
    public function setCreatedAt()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return MenuNode
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set beforeText
     *
     * @param string $beforeText
     *
     * @return MenuNode
     */
    public function setBeforeText($beforeText)
    {
        $this->beforeText = $beforeText;

        return $this;
    }

    /**
     * Get beforeText
     *
     * @return string
     */
    public function getBeforeText()
    {
        return $this->beforeText;
    }

    /**
     * Set afterText
     *
     * @param string $afterText
     *
     * @return MenuNode
     */
    public function setAfterText($afterText)
    {
        $this->afterText = $afterText;

        return $this;
    }

    /**
     * Get afterText
     *
     * @return string
     */
    public function getAfterText()
    {
        return $this->afterText;
    }

    /**
     * Set isSeparator
     *
     * @param boolean $isSeparator
     *
     * @return MenuNode
     */
    public function setIsSeparator($isSeparator)
    {
        $this->isSeparator = $isSeparator;

        return $this;
    }

    /**
     * Get isSeparator
     *
     * @return boolean
     */
    public function getIsSeparator()
    {
        return $this->isSeparator;
    }

    /**
     * Set isMain
     *
     * @param boolean $isMain
     *
     * @return MenuNode
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     *
     * @return MenuNode
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * @return bool
     */
    public function getIsHideChilds()
    {
        return $this->isHideChilds;
    }

    /**
     * @param bool $isHideChilds
     *
     * @return MenuNode
     */
    public function setIsHideChilds($isHideChilds)
    {
        $this->isHideChilds = $isHideChilds;

        return $this;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return MenuNode
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isLinkOnId
     *
     * @param boolean $isLinkOnId
     *
     * @return MenuNode
     */
    public function setIsLinkOnId($isLinkOnId)
    {
        $this->isLinkOnId = $isLinkOnId;

        return $this;
    }

    /**
     * Get isLinkOnId
     *
     * @return boolean
     */
    public function getIsLinkOnId()
    {
        return $this->isLinkOnId;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return MenuNode
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return MenuNode
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set manualUpdatedAt
     *
     * @param \DateTime $manualUpdatedAt
     *
     * @return MenuNode
     */
    public function setManualUpdatedAt($manualUpdatedAt)
    {
        $this->manualUpdatedAt = $manualUpdatedAt;

        return $this;
    }

    /**
     * Get manualUpdatedAt
     *
     * @return \DateTime
     */
    public function getManualUpdatedAt()
    {
        return $this->manualUpdatedAt;
    }

    /**
     * Set manualCreatedAt
     *
     * @param \DateTime $manualCreatedAt
     *
     * @return MenuNode
     */
    public function setManualCreatedAt($manualCreatedAt)
    {
        $this->manualCreatedAt = $manualCreatedAt;

        return $this;
    }

    /**
     * Get manualCreatedAt
     *
     * @return \DateTime
     */
    public function getManualCreatedAt()
    {
        return $this->manualCreatedAt;
    }
    
    /**
     * Set isSearchIndexed.
     *
     * @param bool|null $isSearchIndexed
     *
     * @return MenuNode
     */
    public function setIsSearchIndexed($isSearchIndexed)
    {
        $this->isSearchIndexed = $isSearchIndexed;

        return $this;
    }

    /**
     * Get isSearchIndexed.
     *
     * @return bool|null
     */
    public function geIsSearchIndexed()
    {
        return $this->isSearchIndexed;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function __toString()
    {
        return $this->title ?? '';
    }
}
