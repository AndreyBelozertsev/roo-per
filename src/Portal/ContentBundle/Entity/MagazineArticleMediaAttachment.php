<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MagazineArticleMediaAttachment
 *
 * @ORM\Table(name="magazine_article_media_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MagazineArticleAttachmentRepository")
 */
class MagazineArticleMediaAttachment extends Attachment
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
     * @ORM\OneToOne(targetEntity="MagazineArticle", inversedBy="media")
     * @ORM\JoinColumn(name="magazine_article_id", referencedColumnName="id")
     */
    private $magazineArticle;

    public function getId()
    {
        return parent::getId();
    }

    /**
     * @return MagazineArticle
     */
    public function getMagazineArticle()
    {
        return $this->magazineArticle;
    }

    /**
     * @param mixed $magazineArticle
     */
    public function setMagazineArticle($magazineArticle)
    {
        $this->magazineArticle = $magazineArticle;
    }

    public function __toString()
    {

        return (string)$this->getMagazineArticle()->getId();

    }
}
