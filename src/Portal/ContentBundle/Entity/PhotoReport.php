<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PhotoReport
 *
 * @ORM\Table(name="photo_report")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\PhotoReportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PhotoReport
{
    const PHOTO_REPORT_LIMIT_ON_PAGE = 7;

    const PERMISSIONS_PHOTO_REPORT = [
        'create' => 'create_photo_report',
        'edit' => 'edit_photo_report',
        'delete' => 'delete_photo_report',
        'restore' => 'restore_photo_report'
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = 'empty';

    /**
     * @var string
     *
     * @ORM\Column(name="description_uk", type="text", nullable=true)
     */
    private $descriptionUk;

    /**
     * @var string
     *
     * @ORM\Column(name="description_ru", type="text", nullable=true)
     */
    private $descriptionRu;

    /**
     * @var string
     *
     * @ORM\Column(name="description_en", type="text", nullable=true)
     */
    private $descriptionEn;

    /**
     * @var MenuNode
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MenuNode", inversedBy="photoReports", cascade={"persist"})
     * @ORM\JoinColumn(name="menu_node_id", referencedColumnName="id")
     */
    private $menuNode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="views_counter", type="integer", nullable=false, options={"default":0})
     */
    private $viewsCounter = 0;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\PhotoReportAttachment", mappedBy="photoReport", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "asc"})
     */
    private $attachments;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    private $author;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_search_indexed", type="boolean", nullable=false, options={"default":0})
     */
    private $isSearchIndexed = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
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
     * @return PhotoReport
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
     * Get DescriptionUk
     * @return string
     */
    public function getDescriptionUk()
    {
        return $this->descriptionUk;
    }

    /**
     * Set DescriptionUk
     * @param string $descriptionUk
     * @return PhotoReport;
     */
    public function setDescriptionUk($descriptionUk)
    {
        $this->descriptionUk = $descriptionUk;
        return $this;
    }

    /**
     * Get DescriptionRu
     * @return string
     */
    public function getDescriptionRu()
    {
        return $this->descriptionRu;
    }

    /**
     * Set DescriptionRu
     * @param string $descriptionRu
     * @return PhotoReport;
     */
    public function setDescriptionRu($descriptionRu)
    {
        $this->descriptionRu = $descriptionRu;
        return $this;
    }

    /**
     * Get DescriptionEn
     * @return string
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set DescriptionEn
     * @param string $descriptionEn
     * @return PhotoReport;
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return PhotoReport
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
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return PhotoReport
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set viewsCounter
     *
     * @param integer $viewsCounter
     *
     * @return PhotoReport
     */
    public function setViewsCounter($viewsCounter)
    {
        $this->viewsCounter = $viewsCounter;

        return $this;
    }

    /**
     * Get viewsCounter
     *
     * @return integer
     */
    public function getViewsCounter()
    {
        return $this->viewsCounter;
    }

    /**
     * Add attachment
     *
     * @param \Portal\ContentBundle\Entity\PhotoReportAttachment $attachment
     *
     * @return PhotoReport
     */
    public function addAttachment(PhotoReportAttachment $attachment)
    {
        $this->attachments[] = $attachment;
        $attachment->setPhotoReport($this);

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \Portal\ContentBundle\Entity\PhotoReportAttachment $attachment
     */
    public function removeAttachment(PhotoReportAttachment $attachment)
    {
        $this->attachments->remove($attachment);
        $attachment->setPhotoReport(null);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Get menuNode
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function getMenuNode()
    {
        return $this->menuNode;
    }

    /**
     * Set menuNode
     *
     * @param \Portal\ContentBundle\Entity\MenuNode $menuNode
     *
     * @return PhotoReport
     */
    public function setMenuNode($menuNode)
    {
        $this->menuNode = $menuNode;

        return $this;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return PhotoReport
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set author
     *
     * @param integer $author
     *
     * @return PhotoReport
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set isSearchIndexed.
     *
     * @param bool|null $isSearchIndexed
     *
     * @return PhotoReport
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
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title ?? '';
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        if ($this->getIsPublished()) {
            $this->setPublishedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        if ($this->getIsPublished() && ($this->getPublishedAt() === null)) {
            $this->setPublishedAt(new \DateTime());
        }
    }
}
