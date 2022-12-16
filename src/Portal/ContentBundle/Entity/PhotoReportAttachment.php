<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attachment
 *
 * @ORM\Table(name="photo_report_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\PhotoReportAttachmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PhotoReportAttachment extends Attachment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_order", type="integer", nullable=true, options={"default":"0"})
     */
    private $order = 0;

    /**
     * @var PhotoReport
     *
     * @ORM\ManyToOne(targetEntity="PhotoReport", inversedBy="attachments")
     * @ORM\JoinColumn(name="photo_report_id", referencedColumnName="id")
     */
    private $photoReport;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true, options={"default":0})
     */
    private $isDeleted;

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

//    public static $ALLOWED_FILE_TYPES = [
//        'image/jpeg',
//        'image/png',
//        'image/bmp',
//    ];

//    const MAX_FILE_SIZE = 10485760;

    public function __toString()
    {
        return (string)parent::getId();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * Set photoReport
     *
     * @param PhotoReport $photoReport
     *
     * @return PhotoReportAttachment
     */
    public function setPhotoReport(PhotoReport $photoReport = null)
    {
        $this->photoReport = $photoReport;

        return $this;
    }

    /**
     * Get photoReport
     *
     * @return \Portal\ContentBundle\Entity\photoReport
     */
    public function getPhotoReport()
    {
        return $this->photoReport;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return PhotoReportAttachment
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return PhotoReportAttachment
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
