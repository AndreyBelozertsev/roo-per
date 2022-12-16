<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\FeedbackRepository")
 */
class Feedback
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contentUk", type="string", length=1000)
     */
    private $contentUk;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contentRu", type="string", length=1000, nullable=true)
     */
    private $contentRu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contentEn", type="string", length=1000, nullable=true)
     */
    private $contentEn;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="isFormShown", type="boolean", nullable=true)
     */
    private $isFormShown;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set contentUk.
     *
     * @param string $contentUk
     *
     * @return Feedback
     */
    public function setContentUk($contentUk)
    {
        $this->contentUk = $contentUk;

        return $this;
    }

    /**
     * Get contentUk.
     *
     * @return string
     */
    public function getContentUk()
    {
        return $this->contentUk;
    }

    /**
     * Set contentRu.
     *
     * @param string|null $contentRu
     *
     * @return Feedback
     */
    public function setContentRu($contentRu = null)
    {
        $this->contentRu = $contentRu;

        return $this;
    }

    /**
     * Get contentRu.
     *
     * @return string|null
     */
    public function getContentRu()
    {
        return $this->contentRu;
    }

    /**
     * Set contentEn.
     *
     * @param string|null $contentEn
     *
     * @return Feedback
     */
    public function setContentEn($contentEn = null)
    {
        $this->contentEn = $contentEn;

        return $this;
    }

    /**
     * Get contentEn.
     *
     * @return string|null
     */
    public function getContentEn()
    {
        return $this->contentEn;
    }

    /**
     * Set isFormShown.
     *
     * @param bool|null $isFormShown
     *
     * @return Feedback
     */
    public function setIsFormShown($isFormShown = null)
    {
        $this->isFormShown = $isFormShown;

        return $this;
    }

    /**
     * Get isFormShown.
     *
     * @return bool|null
     */
    public function getIsFormShown()
    {
        return $this->isFormShown;
    }
}
