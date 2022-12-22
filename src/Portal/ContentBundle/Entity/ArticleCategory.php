<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleCategory
 *
 * @ORM\Table(name="article_category")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleCategoryRepository")
 */
class ArticleCategory
{
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
     * @ORM\Column(name="title_uk", type="string", length=255, unique=true)
     */
    private $titleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="title_ru", type="string", length=255, unique=true)
     */
    private $titleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", length=255, unique=true)
     */
    private $titleEn;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\Article", mappedBy="category", cascade={"persist", "remove"})
     */
    private $articles;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true, options={"default":1})
     */
    private $isPublished = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_in_menu", type="boolean", nullable=true, options={"default":0})
     */
    private $showInMenu = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true, options={"default" : 500})
     */
    private $sort;


    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @var ArticleCategoryIconAttachment
     *
     * @ORM\OneToOne(targetEntity="ArticleCategoryIconAttachment", mappedBy="articleCategory", cascade={"persist", "remove"})
     */
    private $icon_attachment;

    /**
     * @var ArticleCategoryThumbnailAttachment
     *
     * @ORM\OneToOne(targetEntity="ArticleCategoryThumbnailAttachment", mappedBy="articleCategory", cascade={"persist", "remove"})
     */
    private $thumbnail_attachment;

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
     * @param string $titleUk
     *
     * @return ArticleCategory
     */
    public function setTitleUk($titleUk)
    {
        $this->titleUk = $titleUk;

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
     * Set titleRu.
     *
     * @param string $titleRu
     *
     * @return ArticleCategory
     */
    public function setTitleRu($titleRu)
    {
        $this->titleRu = $titleRu;

        return $this;
    }

    /**
     * Get titleRu.
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
     * @param string $titleEn
     *
     * @return ArticleCategory
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;

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
     * @return Collection|Article[]
     */
    public function getArticle()
    {
        return $this->articles;
    }

    /**
     * Add article
     *
     * @param Article $article
     * @return ArticleCategory
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
        $article->setCategory($this);

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Article $article
     */
    public function removeArticle(Article $article)
    {
        $this->articles->remove($article);
        $article->setCategory(null);
    }

    /**
     * Set isPublished.
     *
     * @param boolean $isPublished
     * @return ArticleCategory
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
     * Set showInMenu.
     *
     * @param boolean $showInMenu
     * @return ArticleCategory
     */
    public function setShowInMenu($showInMenu)
    {
        $this->showInMenu = $showInMenu;

        return $this;
    }

    /**
     * Get showInMenu.
     *
     * @return bool
     */
    public function getShowInMenu()
    {
        return $this->showInMenu;
    }

    /**
     * Set sort.
     *
     * @param boolean $sort
     * @return ArticleCategory
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return bool
     */
    public function getSort()
    {
        return $this->sort;
    }

    public function __toString()
    {
        return (string)$this->titleRu;
    }

    /**
     * @param ArticleCategoryIconAttachment $icon_attachment
     *
     * @return ArticleCategory
     */
    public function setIconAttachment(ArticleCategoryIconAttachment $icon_attachment)
    {
        $this->icon_attachment = $icon_attachment;
        $icon_attachment->setArticleCategory($this);

        return $this;
    }

    /**
     * Get icon_attachment
     *
     * @return ArticleCategoryIconAttachment
     */
    public function getIconAttachment()
    {
        return $this->icon_attachment;
    }

        /**
     * @param ArticleCategoryThumbnailAttachment $thumbnail_attachment
     *
     * @return ArticleCategory
     */
    public function setThumbnailAttachment(ArticleCategoryThumbnailAttachment $thumbnail_attachment)
    {
        $this->thumbnail_attachment = $thumbnail_attachment;
        $thumbnail_attachment->setArticleCategory($this);

        return $this;
    }

    /**
     * Get thumbnail_attachment
     *
     * @return ArticleCategoryThumbnailAttachment
     */
    public function getThumbnailAttachment()
    {
        return $this->thumbnail_attachment;
    }
}
