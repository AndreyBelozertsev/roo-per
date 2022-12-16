<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MagazineNewspaperAttachment
 * @package Portal\ContentBundle\Entity
 *
 * @ORM\Table(name="magazine_newspaper_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MagazineNewspaperAttachmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MagazineNewspaperAttachment extends Attachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="MagazineNewspaper", inversedBy="attachment")
     * @ORM\JoinColumn(name="magazine_newspaper_id", referencedColumnName="id")
     */
    private $magazineNewspaper;

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
