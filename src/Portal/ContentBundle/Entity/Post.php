<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\PostRepository")
 */
class Post
{
    const PAGE_PAGINATION_LIMIT = 9;
    const HOME_PAGE_LIMIT = 9;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title_uk", type="string", length=1000)
     */
    private $titleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="title_ru", type="string", length=1000, nullable=true)
     */
    private $titleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", length=1000, nullable=true)
     */
    private $titleEn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_name_uk", type="string", length=255, nullable=true)
     */
    private $userNameUk;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_name_ru", type="string", length=255, nullable=true)
     */
    private $userNameRu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_name_en", type="string", length=255, nullable=true)
     */
    private $userNameEn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_position_uk", type="string", length=255, nullable=true)
     */
    private $userPositionUk;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_position_ru", type="string", length=255, nullable=true)
     */
    private $userPositionRu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_position_en", type="string", length=255, nullable=true)
     */
    private $userPositionEn;

    /**
     * @var string
     *
     * @ORM\Column(name="content_uk", type="text")
     */
    private $contentUk;

    /**
     * @var string
     *
     * @ORM\Column(name="content_ru", type="text")
     */
    private $contentRu;

    /**
     * @var string
     *
     * @ORM\Column(name="content_en", type="text")
     */
    private $contentEn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean")
     */
    private $isPublished = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted = false;

    /**
     * @var int
     *
     * @ORM\Column(name="views_counter", type="integer")
     */
    private $viewsCounter = 0;

    /**
     * @var PostAttachment
     *
     * @ORM\OneToOne(targetEntity="PostAttachment", mappedBy="post", cascade={"persist", "remove"})
     */
    private $attachment;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\Comment", mappedBy="post", cascade={"persist"})
     */
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titleUk.
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitleUk($title)
    {
        $this->titleUk = $title;

        return $this;
    }

    /**
     * Get titleUk.
     *
     * @return string
     */
    public function getTitleUk()
    {
        return $this->titleUk;
    }

    /**
     * Set titleUk.
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitleRu($title)
    {
        $this->titleRu = $title;

        return $this;
    }

    /**
     * Get titleUk.
     *
     * @return string
     */
    public function getTitleRu()
    {
        return $this->titleRu;
    }

    /**
     * Set titleEn.
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitleEn($title)
    {
        $this->titleEn = $title;

        return $this;
    }

    /**
     * Get titleEn.
     *
     * @return string
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * Set userNameUk.
     *
     * @param string|null $userName
     *
     * @return Post
     */
    public function setUserNameUk($userName = null)
    {
        $this->userNameUk = $userName;

        return $this;
    }

    /**
     * Get userNameUk.
     *
     * @return string|null
     */
    public function getUserNameUk()
    {
        return $this->userNameUk;
    }

    /**
     * Set userNameRu.
     *
     * @param string|null $userName
     *
     * @return Post
     */
    public function setUserNameRu($userName = null)
    {
        $this->userNameRu = $userName;

        return $this;
    }

    /**
     * Get userNameRu.
     *
     * @return string|null
     */
    public function getUserNameRu()
    {
        return $this->userNameRu;
    }

    /**
     * Set userNameEn.
     *
     * @param string|null $userName
     *
     * @return Post
     */
    public function setUserNameEn($userName = null)
    {
        $this->userNameEn = $userName;

        return $this;
    }

    /**
     * Get userNameEn.
     *
     * @return string|null
     */
    public function getUserNameEn()
    {
        return $this->userNameEn;
    }

    /**
     * Set userPositionUk.
     *
     * @param string|null $userPosition
     *
     * @return Post
     */
    public function setUserPositionUk($userPosition = null)
    {
        $this->userPositionUk = $userPosition;

        return $this;
    }

    /**
     * Get userPositionUk.
     *
     * @return string|null
     */
    public function getUserPositionUk()
    {
        return $this->userPositionUk;
    }

    /**
     * Set userPositionRu.
     *
     * @param string|null $userPosition
     *
     * @return Post
     */
    public function setUserPositionRu($userPosition = null)
    {
        $this->userPositionRu = $userPosition;

        return $this;
    }

    /**
     * Get userPositionRu.
     *
     * @return string|null
     */
    public function getUserPositionRu()
    {
        return $this->userPositionRu;
    }

    /**
     * Set userPositionEn.
     *
     * @param string|null $userPosition
     *
     * @return Post
     */
    public function setUserPositionEn($userPosition = null)
    {
        $this->userPositionEn = $userPosition;

        return $this;
    }

    /**
     * Get userPositionEn.
     *
     * @return string|null
     */
    public function getUserPositionEn()
    {
        return $this->userPositionEn;
    }

    /**
     * Set contentUk.
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContentUk($content)
    {
        $this->contentUk = $content;

        return $this;
    }

    /**
     * Get contentUk.
     *
     * @return string
     */
    public function getContentUk()
    {
        return $this->contentUk;
    }

    /**
     * Set contentRu.
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContentRu($content)
    {
        $this->contentRu = $content;

        return $this;
    }

    /**
     * Get contentRu.
     *
     * @return string
     */
    public function getContentRu()
    {
        return $this->contentRu;
    }

    /**
     * Set contentEn.
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContentEn($content)
    {
        $this->contentEn = $content;

        return $this;
    }

    /**
     * Get contentEn.
     *
     * @return string
     */
    public function getContentEn()
    {
        return $this->contentEn;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isPublished.
     *
     * @param bool $isPublished
     *
     * @return Post
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished.
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return Post
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set viewsCounter.
     *
     * @param int $viewsCounter
     *
     * @return Post
     */
    public function setViewsCounter($viewsCounter)
    {
        $this->viewsCounter = $viewsCounter;

        return $this;
    }

    /**
     * Get viewsCounter.
     *
     * @return int
     */
    public function getViewsCounter()
    {
        return $this->viewsCounter;
    }

    /**
     * @param PostAttachment $attachment
     *
     * @return Post
     */
    public function setAttachment(PostAttachment $attachment)
    {
        $this->attachment = $attachment;
        $attachment->setPost($this);

        return $this;
    }

    /**
     * Get attachment
     *
     * @return PostAttachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Post
     */
    public function addComments(Comment $comment)
    {
        $this->comments[] = $comment;
        $comment->setPost($this);

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComments(Comment $comment)
    {
        $this->comments->remove($comment);
        $comment->setPost(null);
    }
}
