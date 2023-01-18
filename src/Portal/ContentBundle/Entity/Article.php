<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    const TABLE_NAME = 'article';
    const ARTICLES_LIMIT_ON_MAIN_PAGE = 6;
    const ARTICLES_LIMIT_ON_SUBDOMAIN_PAGE = 10;
    const POPULAR_ARTICLES_LIMIT_ON_NEWS_PAGE = 6;
    const SAME_CATEGORY_ARTICLES_LIMIT_ON_BOTTOM_PAGE = 6;
    const RELATED_NEWS_LIMIT = 10;
    const PAGE_PAGINATION_LIMIT = 9;
    const POPULAR_ARTICLES_EXPIRE = 45;

    const PERMISSIONS_ARTICLE = [
        'create' => 'create_article',
        'edit' => 'edit_article',
        'delete' => 'delete_article',
        'restore' => 'restore_article'
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
     * @var ArticleAttachment
     *
     * @ORM\OneToOne(targetEntity="ArticleAttachment", mappedBy="article", cascade={"persist", "remove"})
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
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\ArticleCategory", inversedBy="articles")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var MenuNode
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MenuNode", inversedBy="articles")
     * @ORM\JoinColumn(name="menu_node_id", referencedColumnName="id")
     */
    private $menuNode;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true, options={"default":1})
     */
    private $isPublished = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hot", type="boolean", nullable=true, options={"default":0})
     */
    private $isHot = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_important", type="boolean", nullable=true, options={"default":0})
     */
    private $isImportant = false;

    /**
     * @var string
     *
     * @ORM\Column(name="is_social_enabled", type="boolean", nullable=true, options={"default":0})
     */
    private $socialEnabled = false;

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
     * @var ArticleMediaAttachment
     *
     * @ORM\OneToOne(targetEntity="ArticleMediaAttachment", mappedBy="article", cascade={"persist", "remove"})
     */
    private $media;

    /**
     * @var PhotoReport
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\PhotoReport")
     * @ORM\JoinColumn(name="photo_report_id", referencedColumnName="id")
     */
    private $photoReport;

    /**
     * @var integer
     *
     * @ORM\Column(name="original_article_id", type="integer", nullable=true)
     */
    private $originalArticleId;

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
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\Comment", mappedBy="article", cascade={"persist"})
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
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * Set category
     *
     * @param $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return ArticleCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Article
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
     * Set isHot
     *
     * @param boolean $isHot
     *
     * @return Article
     */
    public function setIsHot($isHot)
    {
        $this->isHot = $isHot;

        return $this;
    }

    /**
     * Get isHot
     *
     * @return bool
     */
    public function getIsHot()
    {
        return $this->isHot;
    }

    /**
     * Set isImportant
     *
     * @param boolean $isImportant
     *
     * @return Article
     */
    public function setIsImportant($isImportant)
    {
        $this->isImportant = $isImportant;

        return $this;
    }

    /**
     * Get isImportant
     *
     * @return bool
     */
    public function getIsImportant()
    {
        return $this->isImportant;
    }

    /**
     * Set socialEnabled
     *
     * @param bool $socialEnabled
     *
     * @return Article
     */
    public function setSocialEnabled($socialEnabled)
    {
        $this->socialEnabled = $socialEnabled;

        return $this;
    }

    /**
     * Get socialEnabled
     *
     * @return bool
     */
    public function getSocialEnabled()
    {
        return $this->socialEnabled;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * @return Article
     */
    public function setMedia($media)
    {
        $this->media = $media;
        $media->setArticle($this);

        return $this;
    }

    /**
     * Get media
     *
     * @return ArticleMediaAttachment
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set photoReport
     *
     * @param \Portal\ContentBundle\Entity\PhotoReport $photoReport|null
     *
     * @return Article
     */
    public function setPhotoReport(PhotoReport $photoReport = null)
    {
        $this->photoReport = $photoReport;

        return $this;
    }

    /**
     * Get photoReport
     *
     * @return \Portal\ContentBundle\Entity\PhotoReport
     */
    public function getPhotoReport()
    {
        return $this->photoReport;
    }

    /**
     * Set originalArticleId
     *
     * @param integer $originalArticleId
     *
     * @return Article
     */
    public function setOriginalArticleId($originalArticleId)
    {
        $this->originalArticleId = $originalArticleId;

        return $this;
    }

    /**
     * Get originalArticleId
     *
     * @return integer
     */
    public function getOriginalArticleId()
    {
        return $this->originalArticleId;
    }

    /**
     * Set originalInstanceCode
     *
     * @param string $originalInstanceCode
     *
     * @return Article
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
     * @return Article
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
     * @return Article
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        $attachment->setArticle($this);

        return $this;
    }

    /**
     * Get attachment
     *
     * @return ArticleAttachment
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
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * @return Article
     */
    public function addComments(Comment $comment)
    {
        $this->comments[] = $comment;
        $comment->setArticle($this);

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
        $comment->setArticle(null);
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
     * @return Article;
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
