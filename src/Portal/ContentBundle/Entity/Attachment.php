<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Portal\HelperBundle\Helper\AttachmentImageHelper;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Attachment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\AttachmentRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "article_attachment" = "ArticleAttachment", "photo_report_attachment" = "PhotoReportAttachment",
 *     "video_report_attachment" = "VideoReportAttachment", "article_media_attachment" = "ArticleMediaAttachment",
 *     "post_attachment" = "PostAttachment",
 *     "magazine_newspaper_attachment" = "MagazineNewspaperAttachment",
 *     "magazine_newspaper_document_attachment" = "MagazineNewspaperDocumentAttachment",
 *     "article_categoty_icon_attachment" = "ArticleCategoryIconAttachment",
 *     "article_categoty_thumbnail_attachment" = "ArticleCategoryThumbnailAttachment",
 *     "magazine_article_attachment" = "MagazineArticleAttachment", 
 *     "magazine_article_media_attachment" = "MagazineArticleMediaAttachment", 
 *     "photo_report_attachment" = "PhotoReportAttachment",
 * })
 *
 * @Vich\Uploadable
 */
abstract class Attachment
{
    const IMAGE = [
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    const VIDEO = [
        'mp4' => 'video/mp4',
        'mkv' => 'video/mkv',
        'avi' => 'video/avi',
    ];

    const DOCUMENTS = [
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xls1' => 'application/vnd.ms-office',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'rtf' => 'application/rtf,.rtf',
        'rtfr' => 'text/richtext',
        'rtft' => 'text/rtf',
        'rtfx' => 'application/x-rtf',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pdf' => 'application/pdf',
        'csv' => 'text/csv',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'txt' => 'text/plain',
        'text' => 'text/vnd.ms-excel'
    ];

    const AUDIO = [
        'mp3' => 'audio/mpeg3',
        'mp3_2' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'wma' => 'audio/x-ms-wma',
        'aac' => 'audio/aac',
        'ac3' => 'audio/ac3',
        'dts' => 'audio/dts',
        'flac' => 'audio/flac'
    ];

    const ARCHIVES = [
        'rar' => 'application/x-rar',
        'rar1' => 'application/x-rar-compressed',
        'rar2' => '.rar',
        'zip' => 'application/zip',
        'archive' => 'application/octet-stream',
    ];

    const LIMIT_FILES = 4;

    const MAX_WIDTH_TEXT_AREAR_IMAGE = 1080;

    const DIR_MAIN_SITE = 'main';
    const FILE_DIR = 'uploads/';
    const MAIN_FILE_DIR = '/uploads/' . self::DIR_MAIN_SITE . '/attachments/';
    const ATTACHMENTS_DIR = 'attachments/';

    const TYPE_BANNERS = 1;
    const TYPE_DOCUMENTS = 2;
    const TYPE_ARTICLES = 3;
    const TYPE_EVENTS = 4;
    const TYPE_PAGES = 5;
    const TYPE_MEDIA = 6;
    const TYPE_PHOTOREPORT = 7;
    const TYPE_SOCIALNETWORK = 8;
    const TYPE_MAGAZINE_ARTICLES = 9;

    const PATH_BANNERS = 'banners/';
    const PATH_DOCUMENTS = 'documents/';
    const PATH_ARTICLES = 'articles/';
    const PATH_MAGAZINE_ARTICLES = 'magazine-articles/';
    const PATH_EVENTS = 'events/';
    const PATH_PAGES = 'pages/';
    const PATH_MEDIA = 'media/';
    const PATH_PHOTOREPORT = 'photoreport/';
    const PATH_INTERVIEW = self::FILE_DIR . 'interviews/';
    const PATH_SOCIALNETWORK = 'socialnetwork/';

    const DEFAULT_FILE_TYPE = 0;
    const FILE_TYPE_DOCUMENT = 1;
    const FILE_TYPE_PHOTO = 2;
    const FILE_TYPE_VIDEO = 3;

    public static $PATH_LIST = [
        self::TYPE_BANNERS => self::PATH_BANNERS,
        self::TYPE_DOCUMENTS => self::PATH_DOCUMENTS,
        self::TYPE_ARTICLES => self::PATH_ARTICLES,
        self::TYPE_EVENTS => self::PATH_EVENTS,
        self::TYPE_PAGES => self::PATH_PAGES,
        self::TYPE_MEDIA => self::PATH_MEDIA,
        self::TYPE_PHOTOREPORT => self::PATH_PHOTOREPORT,
        self::TYPE_SOCIALNETWORK => self::PATH_SOCIALNETWORK,
        self::TYPE_MAGAZINE_ARTICLES => self::PATH_MAGAZINE_ARTICLES
    ];

    const PERMISSIONS_FILE_LIB = [
        'read' => 'read_file_lib',
        'delete' => 'delete_file_lib'
    ];

    const PERMISSIONS_DOCUMENT_LIB = [
        'read' => 'read_document_lib'
    ];

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @Vich\UploadableField(mapping="attachment_mapping", fileNameProperty="preview", mimiType="fileType", size="fileSize", originalName="originalFileName")
     *
     * @var File $file
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="preview_file_url", type="string", length=256, nullable=true)
     */
    protected $previewFileUrl;

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
     * @var int
     *
     * @ORM\Column(name="file_type", type="string", nullable=true)
     */
    private $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="file_description_uk", type="text", nullable=true, options={"default":""})
     */
    private $fileDescriptionUk;

    /**
     * @var string
     *
     * @ORM\Column(name="file_description_ru", type="text", nullable=true, options={"default":""})
     */
    private $fileDescriptionRu;

    /**
     * @var string
     *
     * @ORM\Column(name="file_description_en", type="text", nullable=true, options={"default":""})
     */
    private $fileDescriptionEn;

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
     * Set preview
     *
     * @param string $preview
     *
     * @return Attachment
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
     * Get fileType
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Get FileDescriptionUk
     * @return string
     */
    public function getFileDescriptionUk()
    {
        return $this->fileDescriptionUk;
    }

    /**
     * Set FileDescriptionUk
     * @param string $fileDescriptionUk
     * @return Attachment;
     */
    public function setFileDescriptionUk($fileDescriptionUk)
    {
        $this->fileDescriptionUk = $fileDescriptionUk;
        return $this;
    }

    /**
     * Get FileDescriptionRu
     * @return string
     */
    public function getFileDescriptionRu()
    {
        return $this->fileDescriptionRu;
    }

    /**
     * Set FileDescriptionRu
     * @param string $fileDescriptionRu
     * @return Attachment;
     */
    public function setFileDescriptionRu($fileDescriptionRu)
    {
        $this->fileDescriptionRu = $fileDescriptionRu;
        return $this;
    }

    /**
     * Get FileDescriptionEn
     * @return string
     */
    public function getFileDescriptionEn()
    {
        return $this->fileDescriptionEn;
    }

    /**
     * Set FileDescriptionEn
     * @param string $fileDescriptionEn
     * @return Attachment;
     */
    public function setFileDescriptionEn($fileDescriptionEn)
    {
        $this->fileDescriptionEn = $fileDescriptionEn;
        return $this;
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
     * Set file
     *
     * @param File|UploadedFile $file
     *
     * @return Attachment
     */
    public function setFile($file)
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
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Attachment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Attachment
     */
    public function setOriginalName($originalName)
    {
        $this->originalFileName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalFileName;
    }

    /**
     * Set fileType
     *
     * @param integer $fileType
     *
     * @return Attachment
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }


    /**
     * Set path
     *
     * @param string $previewFileUrl
     *
     * @return Attachment
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

    protected function cropImageAttachment($previewFileUrl, $cropX, $cropY, $cropWidth, $cropHeight)
    {
        $attachmentImageHelper = new AttachmentImageHelper();
        $attachmentImageHelper->cropImage($previewFileUrl, $cropX, $cropY, $cropWidth, $cropHeight);
    }
}
