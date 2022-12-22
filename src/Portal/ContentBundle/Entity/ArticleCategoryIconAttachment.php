<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ArticleCategoryIconAttachment
 * @package Portal\ContentBundle\Entity
 *
 * @ORM\Table(name="article_category_icon_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleCategoryIconAttachmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleCategoryIconAttachment extends Attachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="ArticleCategory", inversedBy="icon_attachment")
     * @ORM\JoinColumn(name="article_category_id", referencedColumnName="id")
     */
    private $articleCategory;

    /**
     * @var integer
     */
    private $cropStartX;

    /**
     * @var integer
     */
    private $cropStartY;

    /**
     * @var integer
     */
    private $cropWidth;

    /**
     * @var integer
     */
    private $cropHeight;

    /**
     * @return int
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * @return ArticleCategory
     */
    public function getArticleCategory()
    {
        return $this->articleCategory;
    }

    /**
     * @param mixed $articleCategory
     */
    public function setArticleCategory($articleCategory)
    {
        $this->articleCategory = $articleCategory;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getArticleCategory()->getId();
    }

    /**
     * @return int
     */
    public function getCropStartX()
    {
        return $this->cropStartX;
    }

    /**
     * @param int $cropStartX
     */
    public function setCropStartX($cropStartX)
    {
        $this->cropStartX = $cropStartX;
    }

    /**
     * @return int
     */
    public function getCropStartY()
    {
        return $this->cropStartY;
    }

    /**
     * @param int $cropStartY
     */
    public function setCropStartY($cropStartY)
    {
        $this->cropStartY = $cropStartY;
    }

    /**
     * @return int
     */
    public function getCropWidth()
    {
        return $this->cropWidth;
    }

    /**
     * @param int $cropWidth
     */
    public function setCropWidth($cropWidth)
    {
        $this->cropWidth = $cropWidth;
    }

    /**
     * @return int
     */
    public function getCropHeight()
    {
        return $this->cropHeight;
    }

    /**
     * @param int $cropHeight
     */
    public function setCropHeight($cropHeight)
    {
        $this->cropHeight = $cropHeight;
    }

    /**
     * @ORM\PostPersist
     */
    public function onPostPersist()
    {
        if ($this->file !== null) {
            $this->cropImageAttachment(
                $this->getPreviewFileUrl(),
                $this->getCropStartX(),
                $this->getCropStartY(),
                $this->getCropWidth(),
                $this->getCropHeight()
            );
        }
    }

    /**
     * @ORM\PostUpdate
     */
    public function onPostUpdate()
    {
        if ($this->file !== null) {
            $this->cropImageAttachment(
                $this->getPreviewFileUrl(),
                $this->getCropStartX(),
                $this->getCropStartY(),
                $this->getCropWidth(),
                $this->getCropHeight()
            );
        }
    }
}
