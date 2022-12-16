<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Portal\AdminBundle\Entity\Instance;

/**
 * Subscribe
 *
 * @ORM\Table(name="article_subscribe", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="article_subscribe_email_instance_id_key", columns={"email", "instance_id"})
 * })
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\ArticleSubscribeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleSubscribe
{
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var Instance
     *
     * @ORM\ManyToOne(targetEntity="Portal\AdminBundle\Entity\Instance")
     * @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     */
    private $instance;

    /**
     * @var string
     *
     * @ORM\Column(name="uid", type="string", length=255)
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


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
     * Set email
     *
     * @param string $email
     *
     * @return ArticleSubscribe
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
     * Set instance
     *
     * @param Instance $instance
     *
     * @return ArticleSubscribe
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     *  Set uid
     *
     * @param string $uid
     *
     * @return ArticleSubscribe
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set createdAt
     *
     * @return ArticleSubscribe
     */
    public function setCreatedAt()
    {
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
        $this->uid = md5($this->email);
        $this->createdAt = new \DateTime();
    }
}
