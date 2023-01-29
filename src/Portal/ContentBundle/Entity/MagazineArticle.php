<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;

/**
 * MagazineArticle
 *
 * @ORM\Table(name="magazine_article")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MagazineArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MagazineArticle
{
    const TABLE_NAME = 'magazine_article';
    const ARTICLES_LIMIT_ON_MAIN_PAGE = 6;
    const ARTICLES_LIMIT_ON_SUBDOMAIN_PAGE = 10;
    const POPULAR_ARTICLES_LIMIT_ON_NEWS_PAGE = 9;
    const SAME_CATEGORY_ARTICLES_LIMIT_ON_BOTTOM_PAGE = 6;
    const RELATED_NEWS_LIMIT = 10;
    const PAGE_PAGINATION_LIMIT = 12;
    const POPULAR_ARTICLES_EXPIRE = 45;

    const PERMISSIONS_ARTICLE = [
        'create' => 'create_magazine_article',
        'edit' => 'edit_magazine_article',
        'delete' => 'delete_magazine_article',
        'restore' => 'restore_magazine_article'
    ];

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var MagazineArticleAttachment
     *
     * @ORM\OneToOne(targetEntity="MagazineArticleAttachment", mappedBy="magazineArticle", cascade={"persist", "remove"})
     */
    private $attachment;

    /**
     * @var string
     *
     * @ORM\Column(name="title_uk", type="string", length=1000)
     */
    private $titleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="title_ru", type="string", length=1000)
     */
    private $titleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", length=1000)
     */
    private $titleEn;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"createdAt", "subtitleUk"}, updatable=false, separator="_", unique=true)
     * @ORM\Column(name="slug", type="string", length=150)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle_uk", type="string", length=255, nullable=true)
     */
    private $subtitleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle_ru", type="string", length=255, nullable=true)
     */
    private $subtitleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle_en", type="string", length=255, nullable=true)
     */
    private $subtitleEn;

    /**
     * @var string
     *
     * @ORM\Column(name="content_uk", type="text", nullable=true)
     */
    private $contentUk;

    /**
     * @var string
     *
     * @ORM\Column(name="content_ru", type="text", nullable=true)
     */
    private $contentRu;

    /**
     * @var string
     *
     * @ORM\Column(name="content_en", type="text", nullable=true)
     */
    private $contentEn;

    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MagazineNewspaper", inversedBy="articles")
     * @ORM\JoinColumn(name="magazine_id", referencedColumnName="id")
     */
    private $magazine;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true, options={"default" : 0})
     */
    private $sort;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true, options={"default":1})
     */
    private $isPublished = false;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="views_counter", type="integer", nullable=false, options={"default":0})
     */
    private $viewsCounter = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="manual_views_counter", type="integer", nullable=false, options={"default":0})
     */
    private $manualViewsCounter = 0;

    /**
     * @var MagazineArticleMediaAttachment
     *
     * @ORM\OneToOne(targetEntity="MagazineArticleMediaAttachment", mappedBy="magazineArticle", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @var integer
     *
     * @ORM\Column(name="original_magazine_article_id", type="integer", nullable=true)
     */
    private $originalMagazineArticleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="old_id", type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @var string
     *
     * @ORM\Column(name="original_instance_code", type="string", length=50, nullable=true)
     */
    private $originalInstanceCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @var string
     *
     * @ORM\Column(name="original_file_path", type="string", length=1000, nullable=true)
     */
    private $originalFilePath;

    /**
     * @var string
     *
     * @ORM\Column(name="related", type="string", length=1000, nullable=true)
     */
    private $related;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_search_indexed", type="boolean", nullable=false, options={"default":0})
     */
    private $isSearchIndexed = true;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\Comment", mappedBy="magazineArticle", cascade={"persist"})
     */
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set slug
     *
     * @param string $slug
     *
     * @return MagazineArticle
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
     * @return MagazineArticle
     */
    public function setMenuNode($menuNode)
    {
        $this->menuNode = $menuNode;

        return $this;
    }

    /**
     * Set author
     *
     * @param integer
     *
     * @return MagazineArticle
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
     * Set magazine
     *
     * @param $magazine
     * @return $this
     */
    public function setMagazine($magazine)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * Get magazine
     *
     * @return MagazineNewspaper
     */
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
    * @return integer
    */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param integer $sort
     * @return MagazineNewspaper
    */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return MagazineArticle
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
     * @return MagazineArticle
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
     * Set createdAt
     *
     * @return MagazineArticle
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
     * Set viewsCounter
     *
     * @param integer $viewsCounter
     *
     * @return MagazineArticle
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
     * Set manualViewsCounter
     *
     * @param integer $manualViewsCounter
     *
     * @return MagazineArticle
     */
    public function setManualViewsCounter($manualViewsCounter)
    {
        $this->manualViewsCounter = $manualViewsCounter;

        return $this;
    }

    /**
     * Get manualViewsCounter
     *
     * @return integer
     */
    public function getManualViewsCounter()
    {
        return $this->manualViewsCounter;
    }


    /**
     * Set media
     *
     * @param Attachment $media
     *
     * @return MagazineArticle
     */
    public function setMedia($media)
    {
        $this->media = $media;
        $media->setMagazineArticle($this);

        return $this;
    }

    /**
     * Get media
     *
     * @return MagazineArticleMediaAttachment
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set originalMagazineArticleId
     *
     * @param integer $originalMagazineArticleId
     *
     * @return MagazineArticle
     */
    public function setOriginalMagazineArticleId($originalMagazineArticleId)
    {
        $this->originalMagazineArticleId = $originalMagazineArticleId;

        return $this;
    }

    /**
     * Get originalMagazineArticleId
     *
     * @return integer
     */
    public function getOriginalMagazineArticleId()
    {
        return $this->originalMagazineArticleId;
    }

    /**
     * Set originalInstanceCode
     *
     * @param string $originalInstanceCode
     *
     * @return MagazineArticle
     */
    public function setOriginalInstanceCode($originalInstanceCode)
    {
        $this->originalInstanceCode = $originalInstanceCode;

        return $this;
    }

    /**
     * Get originalInstanceCode
     *
     * @return string
     */
    public function getOriginalInstanceCode()
    {
        return $this->originalInstanceCode;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return MagazineArticle
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
     * Set attachment
     *
     * @param Attachment $attachment
     *
     * @return MagazineArticle
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        $attachment->setMagazineArticle($this);

        return $this;
    }

    /**
     * Get attachment
     *
     * @return MagazineArticleAttachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set originalFilePath
     *
     * @param string $originalFilePath
     *
     * @return MagazineArticle
     */
    public function setOriginalFilePath($originalFilePath)
    {
        $this->originalFilePath = $originalFilePath;

        return $this;
    }

    /**
     * Get originalFilePath
     *
     * @return string
     */
    public function getOriginalFilePath()
    {
        return $this->originalFilePath;
    }

    /**
     * Set related
     *
     * @param string $related
     *
     * @return MagazineArticle
     */
    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    /**
     * Get related
     *
     * @return string
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return MagazineArticle
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
     * Set isSearchIndexed.
     *
     * @param bool|null $isSearchIndexed
     *
     * @return MagazineArticle
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
     * @return Collection|Comment[]
     */
    public function getComments()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('isPublished', true));

        return $this->comments->matching($criteria);
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return MagazineArticle
     */
    public function addComments(Comment $comment)
    {
        $this->comments[] = $comment;
        $comment->setMagazineArticle($this);

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
        $comment->MagazineArticle(null);
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        if (!$this->getPublishedAt() && $this->getIsPublished()) {
            $this->setPublishedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        if (!$this->getPublishedAt() && $this->getIsPublished()) {
            $this->setPublishedAt(new \DateTime());
        }
    }

    /**
     * Get TitleUk
     * @return string
     */
    public function getTitleUk()
    {
        return $this->titleUk;
    }

    /**
     * Set TitleUk
     * @param string $titleUk
     * @return MagazineArticle;
     */
    public function setTitleUk($titleUk)
    {
        $this->titleUk = $titleUk;
        return $this;
    }

    /**
     * Get TitleRu
     * @return string
     */
    public function getTitleRu()
    {
        return $this->titleRu;
    }

    /**
     * Set TitleRu
     * @param string $titleRu
     * @return MagazineArticle;
     */
    public function setTitleRu($titleRu)
    {
        $this->titleRu = $titleRu;
        return $this;
    }

    /**
     * Get TitleEn
     * @return string
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * Set TitleEn
     * @param string $titleEn
     * @return MagazineArticle;
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;
        return $this;
    }

    /**
     * Get SubtitleUk
     * @return string
     */
    public function getSubtitleUk()
    {
        return $this->subtitleUk;
    }

    /**
     * Set SubtitleUk
     * @param string $subtitleUk
     * @return MagazineArticle;
     */
    public function setSubtitleUk($subtitleUk)
    {
        $this->subtitleUk = $subtitleUk;
        return $this;
    }

    /**
     * Get SubtitleRu
     * @return string
     */
    public function getSubtitleRu()
    {
        return $this->subtitleRu;
    }

    /**
     * Set SubtitleRu
     * @param string $subtitleRu
     * @return MagazineArticle;
     */
    public function setSubtitleRu($subtitleRu)
    {
        $this->subtitleRu = $subtitleRu;
        return $this;
    }

    /**
     * Get SubtitleEn
     * @return string
     */
    public function getSubtitleEn()
    {
        return $this->subtitleEn;
    }

    /**
     * Set SubtitleEn
     * @param string $subtitleEn
     * @return MagazineArticle;
     */
    public function setSubtitleEn($subtitleEn)
    {
        $this->subtitleEn = $subtitleEn;
        return $this;
    }

    /**
     * Get ContentUk
     * @return string
     */
    public function getContentUk()
    {
        return $this->contentUk;
    }

    /**
     * Set ContentUk
     * @param string $contentUk
     * @return MagazineArticle;
     */
    public function setContentUk($contentUk)
    {
        $this->contentUk = $contentUk;
        return $this;
    }

    /**
     * Get ContentRu
     * @return string
     */
    public function getContentRu()
    {
        return $this->contentRu;
    }

    /**
     * Set ContentRu
     * @param string $contentRu
     * @return MagazineArticle;
     */
    public function setContentRu($contentRu)
    {
        $this->contentRu = $contentRu;
        return $this;
    }

    /**
     * Get ContentEn
     * @return string
     */
    public function getContentEn()
    {
        return $this->contentEn;
    }

    /**
     * Set ContentEn
     * @param string $contentEn
     * @return MagazineArticle;
     */
    public function setContentEn($contentEn)
    {
        $this->contentEn = $contentEn;
        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    public function __toString()
    {
        return (string)$this->titleUk;
    }
}
