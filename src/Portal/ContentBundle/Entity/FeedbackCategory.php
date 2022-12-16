<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Portal\HelperBundle\Helper\Transliterator;

/**
 * FeedbackCategory
 *
 * @ORM\Table(name="feedback_category")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\FeedbackCategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class FeedbackCategory
{
    const DEFAULT_GROUP = 'feedback_group';
    const SEX_GROUP = 'sex_group';
    const ADDRESS_GROUP = 'address_group';
    const AGE_GROUP = 'age_group';
    const SOCIAL_GROUP = 'social_group';
    const PRIVILEGE_GROUP = 'privilege_group';

    const PERMISSIONS_FEEDBACK_CATEGORY = [
        'create' => 'create_feedback_category',
        'edit' => 'edit_feedback_category',
        'delete' => 'delete_feedback_category'
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=150, nullable=true, nullable=true)
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true, options={"default":1})
     */
    private $isPublished = true;

    /**
     * @var string
     *
     * @ORM\Column(name="code_group", type="string", length=255, nullable=true, options={"default":"feedback_group"})
     */
    private $codeGroup = self::DEFAULT_GROUP;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order_group", type="integer", nullable=true)
     */
    private $sortOrderGroup;


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
     * Set label
     *
     * @param string $label
     *
     * @return FeedbackCategory
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return FeedbackCategory
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function __toString()
    {
        return $this->label ?? '';
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return FeedbackCategory
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

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCode(Transliterator::transliterate($this->getLabel(), '-'));
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setCode(Transliterator::transliterate($this->getLabel(), '-'));
    }

    /**
     * Set codeGroup
     *
     * @param string $codeGroup
     *
     * @return FeedbackCategory
     */
    public function setCodeGroup($codeGroup)
    {
        $this->codeGroup = $codeGroup;

        return $this;
    }

    /**
     * Get codeGroup
     *
     * @return string
     */
    public function getCodeGroup()
    {
        return $this->codeGroup;
    }

    /**
     * Set sortOrderGroup
     *
     * @param integer $sortOrderGroup
     *
     * @return FeedbackCategory
     */
    public function setSortOrderGroup($sortOrderGroup)
    {
        $this->sortOrderGroup = $sortOrderGroup;

        return $this;
    }

    /**
     * Get sortOrderGroup
     *
     * @return integer
     */
    public function getSortOrderGroup()
    {
        return $this->sortOrderGroup;
    }
}
