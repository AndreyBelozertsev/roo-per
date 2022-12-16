<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractAttachment
{

    /**
     * @var string
     *
     * @ORM\Column(name="preview", type="string", nullable=true, length=255)
     */
    protected $preview;

    /**
     * @var string
     *
     * @ORM\Column(name="original_file_name", type="string", length=500, nullable=true)
     */
    protected $originalFileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", nullable=true)
     */
    protected $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="file_description", type="text", nullable=true, options={"default":""})
     */
    protected $fileDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_size", type="integer", nullable=true, options={"default":"0"})
     */
    protected $fileSize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="file_update_at", type="datetime", nullable=true)
     */
    protected $fileUpdateAt;

    /**
     * @var string
     *
     * @ORM\Column(name="preview_file_url", type="string", length=256, nullable=true)
     *
     */
    protected $previewFileUrl;


    /**
     * Set preview
     *
     * @param string $preview
     *
     * @return static
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
        return $this;
    }

    /**
     * Get preview
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set originalFileName
     *
     * @param string $originalFileName
     *
     * @return static
     */
    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;
        return $this;
    }

    /**
     * Get originalFileName
     *
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     * Set fileType
     *
     * @param string $fileType
     *
     * @return static
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * Get fileType
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set fileDescription
     *
     * @param string $fileDescription
     *
     * @return static
     */
    public function setFileDescription($fileDescription)
    {
        $this->fileDescription = $fileDescription;
        return $this;
    }

    /**
     * Get fileDescription
     *
     * @return string
     */
    public function getFileDescription()
    {
        return $this->fileDescription;
    }

    /**
     * Set fileUpdateAt
     *
     * @param \DateTime $fileUpdateAt
     *
     * @return static
     */
    public function setFileUpdateAt($fileUpdateAt)
    {
        $this->fileUpdateAt = $fileUpdateAt;
        return $this;
    }

    /**
     * Get fileUpdateAt
     *
     * @return \DateTime
     */
    public function getFileUpdateAt()
    {
        return $this->fileUpdateAt;
    }

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     *
     * @return static
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set previewFileUrl
     *
     * @param string $previewFileUrl
     *
     * @return static
     */
    public function setPreviewFileUrl($previewFileUrl)
    {
        $this->previewFileUrl = $previewFileUrl;
        return $this;
    }

    /**
     * Get previewFileUrl
     *
     * @return string
     */
    public function getPreviewFileUrl()
    {
        return $this->previewFileUrl;
    }

}