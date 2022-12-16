<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleMediaAttachment
 *
 * @ORM\Table(name="article_media_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleAttachmentRepository")
 */
class ArticleMediaAttachment extends Attachment
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
     * @ORM\OneToOne(targetEntity="Article", inversedBy="media")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;

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
}
