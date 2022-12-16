<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FeedbackForm
 * @ORM\Table(name="feedback_form", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"title_uk"})})
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\FeedbackFormRepository")
 */
class FeedbackForm
{
    const DEFAULT_MESSAGE_SUCCESS = 'feedback_form.default_message_success';
    const DEFAULT_MESSAGE_ERROR = 'feedback_form.default_message_error';
    const FIELD_SELECTED_DEFAULT_LIST = [
        'subject',
        'categoryId',
        'message',
        'previews',
        'author',
        'phone',
        'email',
        'addressGroup',
        'sexGroup',
        'ageGroup',
        'socialGroup',
        'privilegeGroup',
    ];

    const FIELD_FOR_SELECT = [
        'feedback_form.theme' => 'subject',
        'feedback_form.category' => 'categoryId',
        'feedback_form.body' => 'message',
        'feedback_form.file' => 'previews',
        'feedback_form.author' => 'author',
        'feedback_form.phone' => 'phone',
        'feedback_form.email' => 'email',
        'feedback_form.address_group' => 'addressGroup',
        'feedback_form.sex_group' => 'sexGroup',
        'feedback_form.age_group' => 'ageGroup',
        'feedback_form.social_group' => 'socialGroup',
        'feedback_form.privilege_group' => 'privilegeGroup',
    ];

    const FIELD_ESIA_LIST = [
        'author',
        'phone',
        'email',
//        'addressGroup',
    ];

    const PERMISSIONS_FEEDBACK_FORM = [
        'create' => 'create_feedback_form',
        'edit' => 'edit_feedback_form',
        'delete' => 'delete_feedback_form'
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title_uk", type="string", length=255)
     */
    private $titleUk;

    /**
     * @var string
     *
     * @ORM\Column(name="title_ru", type="string", nullable=true, length=255)
     */
    private $titleRu;

    /**
     * @var string
     *
     * @ORM\Column(name="title_en", type="string", nullable=true, length=255)
     */
    private $titleEn;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"titleUk"}, updatable=false, separator="_", unique=true)
     * @ORM\Column(name="slug", type="string", length=150)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    private $authorId;

    /**
     * @var string
     *
     * @ORM\Column(name="sort_option", type="string", nullable=false, length=200, options={"default":"NULL"}))
     */
    private $sortOptions = null;

    /**
     * @var string
     *
     * @ORM\Column(name="esia_fields", type="string", nullable=true, length=200))
     */
    private $esiaFields;

    /**
     * @var MenuNode
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\MenuNode", inversedBy="feedBacks")
     * @ORM\JoinColumn(name="menu_node_id", referencedColumnName="id")
     */
    private $menuNode;

    /**
     * @var string
     *
     * @ORM\Column(name="visible_option", type="string", nullable=false, length=200, options={"default":""})
     */
    private $visibleOptions = "";

    /**
     * @var string
     *
     * @ORM\Column(name="email_responsible", type="string", nullable=true, length=150, options={"default":""}))
     */
    private $emailResponsible = "";

    /**
     * @var string
     *
     * @ORM\Column(name="message_success", type="string", length=1000, nullable=false, options={"default":"Ваше сообщение успешно отправлено."})
     */
    private $messageSuccess;

    /**
     * @var string
     *
     * @ORM\Column(name="message_error", type="string", length=1000, nullable=false, options={"default":"При отправке сообщения произошла ошибка."})
     */
    private $messageError;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_registered_user", type="boolean", nullable=true, options={"default":0})
     */
    private $isRegisteredUser = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_agree_personal_data", type="boolean", nullable=true, options={"default":1})
     */
    private $isAgreePersonalData = true;

    /**
     * @var string
     *
     * @ORM\Column(name="description_uk", type="text", nullable=true)
     */
    private $descriptionUk;

    /**
     * @var string
     *
     * @ORM\Column(name="description_ru", type="text", nullable=true)
     */
    private $descriptionRu;

    /**
     * @var string
     *
     * @ORM\Column(name="description_en", type="text", nullable=true)
     */
    private $descriptionEn;

    private $properties = array();

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    public function __set($name, $value) {
        $this->properties[$name] = $value;
    }

    /**
     * Get menuNode
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function getMenuNode()
    {
        return $this->menuNode;
    }

    /**
     * Set menuNode
     *
     * @param \Portal\ContentBundle\Entity\MenuNode $menuNode
     *
     * @return FeedbackForm
     */
    public function setMenuNode($menuNode)
    {
        $this->menuNode = $menuNode;

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return FeedbackForm
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set authorId
     *
     * @param integer $authorId
     *
     * @return FeedbackForm
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Get authorId
     *
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set sortOptions
     *
     * @param string $sortOptions
     *
     * @return FeedbackForm
     */
    public function setSortOptions($sortOptions)
    {
        $this->sortOptions = $sortOptions;

        return $this;
    }

    /**
     * Get sortOptions
     *
     * @return string
     */
    public function getSortOptions()
    {
        return $this->sortOptions;
    }

    /**
     * Set visibleOptions
     *
     * @param string $visibleOptions
     *
     * @return FeedbackForm
     */
    public function setVisibleOptions($visibleOptions)
    {
        $this->visibleOptions = $visibleOptions;

        return $this;
    }

    /**
     * Get visibleOptions
     *
     * @return string
     */
    public function getVisibleOptions()
    {
        return $this->visibleOptions;
    }

    /**
     * Set emailResponsible
     *
     * @param string $emailResponsible
     *
     * @return FeedbackForm
     */
    public function setEmailResponsible($emailResponsible)
    {
        $this->emailResponsible = $emailResponsible;

        return $this;
    }

    /**
     * Get emailResponsible
     *
     * @return string
     */
    public function getEmailResponsible()
    {
        return $this->emailResponsible;
    }

    /**
     * Set messageSuccess
     *
     * @param string $messageSuccess
     *
     * @return FeedbackForm
     */
    public function setMessageSuccess($messageSuccess)
    {
        $this->messageSuccess = $messageSuccess;

        return $this;
    }

    /**
     * Get messageSuccess
     *
     * @return string
     */
    public function getMessageSuccess()
    {
        return $this->messageSuccess;
    }

    /**
     * Set messageError
     *
     * @param string $messageError
     *
     * @return FeedbackForm
     */
    public function setMessageError($messageError)
    {
        $this->messageError = $messageError;

        return $this;
    }

    /**
     * Get messageSuccess
     *
     * @return string
     */
    public function getMessageError()
    {
        return $this->messageError;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return FeedbackForm
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    public function __toString()
    {
        return $this->titleUk ?? '';
    }

    /**
     * Set isRegisteredUser
     *
     * @param boolean $isRegisteredUser
     *
     * @return FeedbackForm
     */
    public function setIsRegisteredUser($isRegisteredUser)
    {
        $this->isRegisteredUser = $isRegisteredUser;

        return $this;
    }

    /**
     * Get isRegisteredUser
     *
     * @return boolean
     */
    public function getIsRegisteredUser()
    {
        return $this->isRegisteredUser;
    }

    /**
     * Set esiaFields
     *
     * @param string $esiaFields
     *
     * @return FeedbackForm
     */
    public function setEsiaFields($esiaFields)
    {
        $this->esiaFields = $esiaFields;

        return $this;
    }

    /**
     * Get esiaFields
     *
     * @return string
     */
    public function getEsiaFields()
    {
        return $this->esiaFields;
    }

    /**
     * Set isAgreePersonalData.
     *
     * @param bool|null $isAgreePersonalData
     *
     * @return FeedbackForm
     */
    public function setIsAgreePersonalData($isAgreePersonalData = null)
    {
        $this->isAgreePersonalData = $isAgreePersonalData;

        return $this;
    }

    /**
     * Get isAgreePersonalData.
     *
     * @return bool|null
     */
    public function getIsAgreePersonalData()
    {
        return $this->isAgreePersonalData;
    }

    /**
     * Get TitleUk
     * @return string
     */
    public function getTitleUk()
    {
        return $this->titleUk;
    }

    /**
     * Set TitleUk
     * @param string $titleUk
     * @return FeedbackForm;
     */
    public function setTitleUk($titleUk)
    {
        $this->titleUk = $titleUk;
        return $this;
    }

    /**
     * Get TitleRu
     * @return string
     */
    public function getTitleRu()
    {
        return $this->titleRu;
    }

    /**
     * Set TitleRu
     * @param string $titleRu
     * @return FeedbackForm;
     */
    public function setTitleRu($titleRu)
    {
        $this->titleRu = $titleRu;
        return $this;
    }

    /**
     * Get TitleEn
     * @return string
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * Set TitleEn
     * @param string $titleEn
     * @return FeedbackForm;
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;
        return $this;
    }

    /**
     * Get DescriptionUk
     * @return string
     */
    public function getDescriptionUk()
    {
        return $this->descriptionUk;
    }

    /**
     * Set DescriptionUk
     * @param string $descriptionUk
     * @return FeedbackForm;
     */
    public function setDescriptionUk($descriptionUk)
    {
        $this->descriptionUk = $descriptionUk;
        return $this;
    }

    /**
     * Get DescriptionRu
     * @return string
     */
    public function getDescriptionRu()
    {
        return $this->descriptionRu;
    }

    /**
     * Set DescriptionRu
     * @param string $descriptionRu
     * @return FeedbackForm;
     */
    public function setDescriptionRu($descriptionRu)
    {
        $this->descriptionRu = $descriptionRu;
        return $this;
    }

    /**
     * Get DescriptionEn
     * @return string
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set DescriptionEn
     * @param string $descriptionEn
     * @return FeedbackForm;
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

}
