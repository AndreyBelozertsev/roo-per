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


    public function __construct()
    {
        $this->articles = new ArrayCollection();
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


    public function __toString()
    {
        return (string)$this->titleRu;
    }
}
