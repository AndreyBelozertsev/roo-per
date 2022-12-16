<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MagazineNewspaperDocumentAttachment
 * @package Portal\ContentBundle\Entity
 *
 * @ORM\Table(name="magazine_newspaper_document_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MagazineNewspaperDocumentAttachmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MagazineNewspaperDocumentAttachment extends Attachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="MagazineNewspaper", inversedBy="document_attachment")
     * @ORM\JoinColumn(name="magazine_newspaper_id", referencedColumnName="id")
     */
    private $magazineNewspaper;

    /**
     * @return int
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * @return MagazineNewspaper
     */
    public function getMagazineNewspaper()
    {
        return $this->magazineNewspaper;
    }

    /**
     * @param mixed $magazineNewspaper
     */
    public function setMagazineNewspaper($magazineNewspaper)
    {
        $this->magazineNewspaper = $magazineNewspaper;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getMagazineNewspaper()->getId();
    }


}
