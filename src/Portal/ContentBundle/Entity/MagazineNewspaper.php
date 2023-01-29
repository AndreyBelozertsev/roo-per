<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MagazineNewspaper
 *
 * @ORM\Table(name="magazine_newspaper")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MagazineNewspaperRepository")
 */
class MagazineNewspaper
{
    const PAGE_PAGINATION_LIMIT = 12;
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
     * @ORM\Column(name="title_uk", type="string", length=255)
     */
    private $titleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="title_ru", type="string", length=255, nullable=true)
     */
    private $titleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", length=255, nullable=true)
     */
    private $titleEn;

    /**
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\MagazineArticle", mappedBy="magazine", cascade={"persist", "remove"})
     */
    private $magazineArticles;

    /**
     * @var string
     *
     * @ORM\Column(name="type_of", type="string", length=255,)
     */
    private $type_of;

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
     * @var MagazineNewspaperAttachment
     *
     * @ORM\OneToOne(targetEntity="MagazineNewspaperAttachment", mappedBy="magazineNewspaper", cascade={"persist", "remove"})
     */
    private $attachment;

    /**
     * @var MagazineNewspaperDocumentAttachment
     *
     * @ORM\OneToOne(targetEntity="MagazineNewspaperDocumentAttachment", mappedBy="magazineNewspaper", cascade={"persist", "remove"})
     */
    private $document_attachment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->magazineArticles = new ArrayCollection();
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
     * @return MagazineNewspaper
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
     * @return MagazineNewspaper
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
     * @return MagazineNewspaper
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
     * @return Collection|Article[]
     */
    public function getMagazineArticle()
    {
        return $this->magazineArticles;
    }

    /**
     * Add article
     *
     * @param MagazineArticle $magazineArticle
     * @return MagazineNewspaper
     */
    public function addMagazineArticle(MagazineArticle $magazineArticle)
    {
        $this->magazineArticles[] = $magazineArticle;
        $magazineArticle->setMagazine($this);

        return $this;
    }

    /**
     * Remove magazineArticle
     *
     * @param MagazineArticle $magazineArticle
     */
    public function removeMagazineArticle(MagazineArticle $magazineArticle)
    {
        $this->magazineArticles->remove($magazineArticle);
        $magazineArticle->setMagazine(null);
    }

    /**
     * Set type_of.
     *
     * @param string $type_of
     *
     * @return MagazineNewspaper
     */
    public function setTypeOf($type_of)
    {
        $this->type_of = $type_of;

        return $this;
    }

    /**
     * Get type_of.
     *
     * @return string
     */
    public function getTypeOf()
    {
        return $this->type_of;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return MagazineNewspaper
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
     * @return MagazineNewspaper
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
     * @return MagazineNewspaper
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
     * @param MagazineNewspaperAttachment $attachment
     *
     * @return MagazineNewspaper
     */
    public function setAttachment(MagazineNewspaperAttachment $attachment)
    {
        $this->attachment = $attachment;
        $attachment->setMagazineNewspaper($this);

        return $this;
    }

    /**
     * Get attachment
     *
     * @return MagazineNewspaperAttachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param MagazineNewspaperDocumentAttachment $document_attachment
     *
     * @return MagazineNewspaper
     */
    public function setDocumentAttachment(MagazineNewspaperDocumentAttachment $document_attachment)
    {
        $this->document_attachment = $document_attachment;
        $document_attachment->setMagazineNewspaper($this);

        return $this;
    }

    /**
     * Get document_attachment
     *
     * @return MagazineNewspaperDocumentAttachment
     */
    public function getDocumentAttachment()
    {
        return $this->document_attachment;
    }

}
