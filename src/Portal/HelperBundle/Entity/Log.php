<?php

namespace Portal\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="entity_log")
 * @ORM\Entity(repositoryClass="Portal\HelperBundle\Repository\LogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    const ENTITY_TYPE_ARTICLE = 'article';
    const ENTITY_TYPE_EVENT = 'event';
    const ENTITY_TYPE_DOCUMENT = 'document';
    const ENTITY_TYPE_PHOTO_REPORT = 'photo_report';
    const ENTITY_TYPE_VIDEO_REPORT = 'video_report';
    const ENTITY_TYPE_HEAD = 'head';
    const ENTITY_TYPE_FEEDBACK_FORM = 'feedback_form';
    const ENTITY_TYPE_STRUCTURE = 'structure';
    const ENTITY_TYPE_INTERVIEW = 'interview';
    const ENTITY_TYPE_QUIZ = 'quiz';
    const ENTITY_TYPE_SLIDER = 'slider';
    const ENTITY_TYPE_BANNER = 'banner';
    const ENTITY_TYPE_WIDGET = 'widget';
    const ENTITY_TYPE_MATERIAL = 'material';

    const ACTION_TYPE_CREATE = 'create';
    const ACTION_TYPE_EDIT = 'edit';
    const ACTION_TYPE_DELETE = 'delete';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_type", type="string", length=50, nullable=true)
     */
    private $entityType;

    /**
     * @var int
     *
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="action_type", type="string", length=50, nullable=true)
     */
    private $actionType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="instance_code", type="string", nullable=true)
     */
    private $instanceCode;

    public static $LOG_MIGRATION_VERSION_LIST = array(
        '20171108091805',
        '20171108125506',
        '20171109121017',
        '20180924110904',
        '20181009134803',
    );


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
     * Set message
     *
     * @param string $message
     *
     * @return Log
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Log
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
        $this->createdAt = new \DateTime();
    }

    /**
     * Set entityType
     *
     * @param string $entityType
     *
     * @return Log
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Get entityType
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     *
     * @return Log
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Log
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set actionType
     *
     * @param string $actionType
     *
     * @return Log
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * Get actionType
     *
     * @return string
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Set instanceCode.
     *
     * @param string|null $instanceCode
     *
     * @return Log
     */
    public function setInstanceCode($instanceCode = null)
    {
        $this->instanceCode = $instanceCode;

        return $this;
    }

    /**
     * Get instanceCode.
     *
     * @return string|null
     */
    public function getInstanceCode()
    {
        return $this->instanceCode;
    }
}
