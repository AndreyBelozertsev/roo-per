<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleAttachment
 *
 * @ORM\Table(name="article_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleAttachmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleAttachment extends Attachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="Article", inversedBy="attachment")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer
     *
     */
    private $cropStartX;

    /**
     * @var integer
     *
     */
    private $cropStartY;

    /**
     * @var integer
     *
     */
    private $cropWidth;

    /**
     * @var integer
     *
     */
    private $cropHeight;

    public function getId()
    {
        return parent::getId();
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function __toString()
    {

        return (string)$this->getArticle()->getId();

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
            $this->cropImageAttachment($this->getPreviewFileUrl(), $this->getCropStartX(), $this->getCropStartY(), $this->getCropWidth(), $this->getCropHeight());
        }
    }

    /**
     * @ORM\PostUpdate
     */
    public function onPostUpdate()
    {
        if ($this->file !== null) {
            $this->cropImageAttachment($this->getPreviewFileUrl(), $this->getCropStartX(), $this->getCropStartY(), $this->getCropWidth(), $this->getCropHeight());
        }
    }
}
