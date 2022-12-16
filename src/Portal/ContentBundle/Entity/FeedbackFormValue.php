<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

//use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;

/**
 * FeedbackFormValue
 *
 * @ORM\Table(name="feedback_form_value")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\FeedbackFormValueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class FeedbackFormValue
{
    const MAX_COUNTER_UPLOAD_FILE = 10;
    const MAX_SIZE_UPLOAD_FILE = 10485760;
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
     * @var FeedbackForm
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackForm")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     *
     * */
    private $form;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=1000, nullable=true)
     */
    private $subject;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     *
     * */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=1000, nullable=true)
     */
    private $email;

    /**
     * @var FeedbackFormAttachment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\FeedbackFormAttachment", mappedBy="feedbackForm", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
     *
     * */
    private $previews;

    /**
     * @var FeedbackFormAttachment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\FeedbackFormAttachment", mappedBy="feedbackForm", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
     *
     * */
    private $references;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="address_group", referencedColumnName="id", nullable=true)
     *
     * */
    private $addressGroup;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="sex_group", referencedColumnName="id", nullable=true)
     *
     * */
    private $sexGroup;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="age_group", referencedColumnName="id", nullable=true)
     *
     * */
    private $ageGroup;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="social_group", referencedColumnName="id", nullable=true)
     *
     * */
    private $socialGroup;

    /**
     * @var FeedbackCategory
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\FeedbackCategory")
     * @ORM\JoinColumn(name="privilege_group", referencedColumnName="id", nullable=true)
     *
     * */
    private $privilegeGroup;

//    /**
//     * @ValidCaptcha(
//     *      message = "feedback_form_value.captcha_error"
//     * )
//     */
    
    /**
     *
     * @var type 
     */
//    protected $captchaCode;

    public function __construct()
    {
        $this->previews = new ArrayCollection();
        $this->references = new ArrayCollection();
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
     * Set form
     *
     * @param \Portal\ContentBundle\Entity\FeedbackForm $form
     *
     * @return FeedbackFormValue
     */
    public function setForm(\Portal\ContentBundle\Entity\FeedbackForm $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Portal\ContentBundle\Entity\FeedbackForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return FeedbackFormValue
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set categoryId
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory|null $categoryId
     *
     * @return FeedbackFormValue
     */
    public function setCategoryId(\Portal\ContentBundle\Entity\FeedbackCategory $categoryId = null)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return FeedbackFormValue
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return FeedbackFormValue
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return FeedbackFormValue
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return FeedbackFormValue
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add preview
     *
     * @param \Portal\ContentBundle\Entity\FeedbackFormAttachment $preview
     *
     * @return FeedbackFormValue
     */
    public function addPreview(\Portal\ContentBundle\Entity\FeedbackFormAttachment $preview)
    {
        $this->previews[] = $preview;
        $preview->setFeedbackForm($this);

        return $this;
    }

    /**
     * Remove preview
     *
     * @param \Portal\ContentBundle\Entity\FeedbackFormAttachment $preview
     */
    public function removePreview(\Portal\ContentBundle\Entity\FeedbackFormAttachment $preview)
    {
        $this->previews->removeElement($preview);
    }

    /**
     * Get previews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreviews()
    {
        return $this->previews;
    }

    /**
     * Add reference
     *
     * @param \Portal\ContentBundle\Entity\FeedbackFormAttachment $reference
     *
     * @return FeedbackFormValue
     */
    public function addReference(\Portal\ContentBundle\Entity\FeedbackFormAttachment $reference)
    {
        $this->references[] = $reference;
        $reference->setFeedbackForm($this);

        return $this;
    }

    /**
     * Remove reference
     *
     * @param \Portal\ContentBundle\Entity\FeedbackFormAttachment $reference
     */
    public function removeReference(\Portal\ContentBundle\Entity\FeedbackFormAttachment $reference)
    {
        $this->references->removeElement($reference);
    }

    /**
     * Get reference
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Set createdAt
     *
     * @return FeedbackFormValue
     */
    public function setCreatedAt()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
            $this->setCreatedAt(new \DateTime());
    }

    /**
     * Set addressGroup
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory $addressGroup
     *
     * @return FeedbackFormValue
     */
    public function setAddressGroup(\Portal\ContentBundle\Entity\FeedbackCategory $addressGroup = null)
    {
        $this->addressGroup = $addressGroup;

        return $this;
    }

    /**
     * Get addressGroup
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getAddressGroup()
    {
        return $this->addressGroup;
    }

    /**
     * Set sexGroup
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory $sexGroup
     *
     * @return FeedbackFormValue
     */
    public function setSexGroup(\Portal\ContentBundle\Entity\FeedbackCategory $sexGroup = null)
    {
        $this->sexGroup = $sexGroup;

        return $this;
    }

    /**
     * Get sexGroup
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getSexGroup()
    {
        return $this->sexGroup;
    }

    /**
     * Set ageGroup
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory $ageGroup
     *
     * @return FeedbackFormValue
     */
    public function setAgeGroup(\Portal\ContentBundle\Entity\FeedbackCategory $ageGroup = null)
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }

    /**
     * Get ageGroup
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * Set socialGroup
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory $socialGroup
     *
     * @return FeedbackFormValue
     */
    public function setSocialGroup(\Portal\ContentBundle\Entity\FeedbackCategory $socialGroup = null)
    {
        $this->socialGroup = $socialGroup;

        return $this;
    }

    /**
     * Get socialGroup
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getSocialGroup()
    {
        return $this->socialGroup;
    }

    /**
     * Set privilegeGroup
     *
     * @param \Portal\ContentBundle\Entity\FeedbackCategory $privilegeGroup
     *
     * @return FeedbackFormValue
     */
    public function setPrivilegeGroup(\Portal\ContentBundle\Entity\FeedbackCategory $privilegeGroup = null)
    {
        $this->privilegeGroup = $privilegeGroup;

        return $this;
    }

    /**
     * Get privilegeGroup
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function getPrivilegeGroup()
    {
        return $this->privilegeGroup;
    }

//    public function getCaptchaCode()
//    {
//        return $this->captchaCode;
//    }
//
//    public function setCaptchaCode($captchaCode)
//    {
//        $this->captchaCode = $captchaCode;
//    }

    /**
     * @Assert\Callback(groups={"email_phone_validation"})
     */
    public function validate(ExecutionContextInterface $context, $payload){

        if ($this->getPhone() === null && $this->getEmail() === null) {
            $context->buildViolation('feedback_form_value.email_or_phone_blank')
                ->atPath('email')
                ->addViolation();
            $context->buildViolation('feedback_form_value.email_or_phone_blank')
                ->atPath('phone')
                ->addViolation();
        }
    }
}
