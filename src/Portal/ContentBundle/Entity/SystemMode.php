<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemMode
 *
 * @ORM\Table(name="system_mode")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\SystemModeRepository")
 */
class SystemMode
{
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_message", nullable=true, type="string", length=255)
     */
    private $notificationMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="mode_message", nullable=true, type="string", length=255)
     */
    private $modeMessage;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active_notification", nullable=true, type="boolean", options={"default":0})
     */
    private $isActiveNotification = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active_mode", type="boolean", nullable=true, options={"default":0})
     */
    private $isActiveMode = false;

    const SERVICE_MODE = 'service_mode';

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
     * Set code
     *
     * @param string $code
     *
     * @return SystemMode
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

    /**
     * Set notificationMessage
     *
     * @param string $notificationMessage
     *
     * @return SystemMode
     */
    public function setNotificationMessage($notificationMessage)
    {
        $this->notificationMessage = $notificationMessage;

        return $this;
    }

    /**
     * Get notificationMessage
     *
     * @return string
     */
    public function getNotificationMessage()
    {
        return $this->notificationMessage;
    }

    /**
     * Set modeMessage
     *
     * @param string $modeMessage
     *
     * @return SystemMode
     */
    public function setModeMessage($modeMessage)
    {
        $this->modeMessage = $modeMessage;

        return $this;
    }

    /**
     * Get modeMessage
     *
     * @return string
     */
    public function getModeMessage()
    {
        return $this->modeMessage;
    }

    /**
     * Set isActiveNotification
     *
     * @param boolean $isActiveNotification
     *
     * @return SystemMode
     */
    public function setIsActiveNotification($isActiveNotification)
    {
        $this->isActiveNotification = $isActiveNotification;

        return $this;
    }

    /**
     * Get isActiveNotification
     *
     * @return boolean
     */
    public function getIsActiveNotification()
    {
        return $this->isActiveNotification;
    }

    /**
     * Set isActiveMode
     *
     * @param boolean $isActiveMode
     *
     * @return SystemMode
     */
    public function setIsActiveMode($isActiveMode)
    {
        $this->isActiveMode = $isActiveMode;

        return $this;
    }

    /**
     * Get isActiveMode
     *
     * @return boolean
     */
    public function getIsActiveMode()
    {
        return $this->isActiveMode;
    }
}
