<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Widget
 *
 * @ORM\Table(name="widget")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\WidgetRepository")
 */
class Widget
{
    const VIEW_IN_WIDGET_PANEL_TYPE = 1;
    const VIEW_IN_WIDGET_CONTENT_TYPE = 2;

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
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=false, options={"default":1})
     */
    private $isPublished = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="views_widget_type", type="integer", nullable=false, options={"default":1})
     */
    private $viewWidgetType = 1;


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
     * @return Widget
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Widget
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
     * Set author
     *
     * @param integer $author
     *
     * @return Widget
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Widget
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Widget
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set viewWidgetType
     *
     * @param integer $viewWidgetType
     *
     * @return Widget
     */
    public function setViewWidgetType($viewWidgetType)
    {
        $this->viewWidgetType = $viewWidgetType;

        return $this;
    }

    /**
     * Get viewWidgetType
     *
     * @return integer
     */
    public function getViewWidgetType()
    {
        return $this->viewWidgetType;
    }

    /**
     * @return string
     *
     */
    public function __toString()
    {
        return $this->label ?? '';
    }
}
