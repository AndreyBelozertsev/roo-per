<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FeedbackFormAttachment
 *
 * @ORM\Table(name="feedback_form_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\FeedbackFormAttachmentRepository")
 * @Vich\Uploadable
 */
class FeedbackFormAttachment extends AbstractAttachment
{
    const FILE_TYPE = 2;
    const MAX_FILE_SIZE = 10485760;
    const MAX_FILE_ATTACHMENT = 10;
    public static $ALLOWED_FILE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/bmp',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * @var FeedbackFormValue
     *
     * @ORM\ManyToOne(targetEntity="FeedbackFormValue", inversedBy="previews", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="feedback_form_value_id", referencedColumnName="id", nullable=true)
     * })
     **/
    private $feedbackForm;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=2048, nullable=true)
     */
    private $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     *
     * @Vich\UploadableField(mapping="feedback_form_attachment_mapping", fileNameProperty="preview", size="fileSize", mimiType="fileType", originalName="originalFileName")
     *
     * @var File $file
     */
    public $file;

    /**
     *
     * @param File|UploadedFile|null $file
     * @return FeedbackFormAttachment
     */
    public function setFile($file = null)
    {
        $this->file = $file;
        if ($file instanceof UploadedFile) {
            $this->setFileUpdateAt(new \DateTime());
        }
        return $this;
    }

    /**
     * Gets file instance
     *
     * @return File|UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function __toString() {
        if ( isset($this->label) ) {
            return $this->label;
        } else {
            return '';
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set feedbackForm
     *
     * @param \Portal\ContentBundle\Entity\FeedbackFormValue $feedbackForm
     *
     * @return FeedbackFormAttachment
     */
    public function setFeedbackForm(\Portal\ContentBundle\Entity\FeedbackFormValue $feedbackForm = null)
    {
        $this->feedbackForm = $feedbackForm;

        return $this;
    }

    /**
     * Get feedbackForm
     *
     * @return \Portal\ContentBundle\Entity\FeedbackFormValue
     */
    public function getFeedbackForm()
    {
        return $this->feedbackForm;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return FeedbackFormAttachment
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
