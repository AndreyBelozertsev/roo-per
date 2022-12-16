<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoReport
 *
 * @ORM\Table(name="video_report")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\VideoReportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class VideoReport
{
    const VIDEO_REPORT_LIMIT_ON_PAGE = 7;

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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var MenuNode
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MenuNode", inversedBy="videoReports", cascade={"persist"})
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
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="views_counter", type="integer", nullable=false, options={"default":0})
     */
    private $viewsCounter = 0;

    /**
     * @ORM\OneToOne(targetEntity="VideoReportAttachment", mappedBy="videoReport", cascade={"persist","remove"})
     */
    private $attachment;

    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    private $author;


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
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set attachment
     *
     * @param VideoReportAttachment $attachment
     *
     * @return VideoReport
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        $attachment->setVideoReport($this);

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return VideoReport
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
     * Set description
     *
     * @param string $description
     *
     * @return VideoReport
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return VideoReport
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
     * @return VideoReport
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
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return VideoReport
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param $viewsCounter
     * @return $this
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
     * @return VideoReport
     */
    public function setMenuNode($menuNode)
    {
        $this->menuNode = $menuNode;

        return $this;
    }

    /**
     * Set author
     *
     * @param integer $author
     *
     * @return VideoReport
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
        $this->setUpdateAt(new \DateTime());
        if ($this->getIsPublished() && ($this->getPublishedAt() === null)) {
            $this->setPublishedAt(new \DateTime());
        }
    }
}
