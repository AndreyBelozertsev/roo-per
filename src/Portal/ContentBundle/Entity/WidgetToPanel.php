<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WidgetToPanel
 *
 * @ORM\Table(name="widget2panel")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\WidgetToPanelRepository")
 */
class WidgetToPanel
{
    const PERMISSIONS_WIDGET = [
        'create' => 'create_widget',
        'edit' => 'edit_widget',
        'delete' => 'delete_widget'
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=1000, options={"default":""})
     */
    private $title;

    /**
     * @var Widget
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\Widget")
     * @ORM\JoinColumn(name="widget_id", referencedColumnName="id")
     */
    private $widgetId;

    /**
     * @var WidgetPanel
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\WidgetPanel")
     * @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     */
    private $panelId;

    /**
     * @var WidgetParam
     *
     * @ORM\OneToMany(targetEntity="Portal\ContentBundle\Entity\WidgetParam", mappedBy="widgetToPanelId", cascade={"all"})
     * @ORM\JoinColumn(name="widget_param_id", referencedColumnName="id")
     */
    private $widgetParam;

    /**
     * @var int
     *
     * @ORM\Column(name="widget_order", type="integer", nullable=true, options={"default":0})
     */
    private $widgetOrder = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=false, options={"default":1})
     */
    private $isPublished = true;

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
     * Set widgetOrder
     *
     * @param integer $widgetOrder
     *
     * @return WidgetToPanel
     */
    public function setWidgetOrder($widgetOrder)
    {
        $this->widgetOrder = $widgetOrder;

        return $this;
    }

    /**
     * Get widgetOrder
     *
     * @return integer
     */
    public function getWidgetOrder()
    {
        return $this->widgetOrder;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return WidgetToPanel
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set widgetId
     *
     * @param \Portal\ContentBundle\Entity\Widget $widgetId
     *
     * @return WidgetToPanel
     */
    public function setWidgetId(Widget $widgetId = null)
    {
        $this->widgetId = $widgetId;

        return $this;
    }

    /**
     * Get widgetId
     *
     * @return \Portal\ContentBundle\Entity\Widget
     */
    public function getWidgetId()
    {
        return $this->widgetId;
    }

    /**
     * Set panelId
     *
     * @param \Portal\ContentBundle\Entity\WidgetPanel $panelId
     *
     * @return WidgetToPanel
     */
    public function setPanelId(WidgetPanel $panelId = null)
    {
        $this->panelId = $panelId;

        return $this;
    }

    /**
     * Get panelId
     *
     * @return \Portal\ContentBundle\Entity\WidgetPanel
     */
    public function getPanelId()
    {
        return $this->panelId;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return WidgetToPanel
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
     * Get WidgetParam
     *
     * @return WidgetParam
     */
    public function getWidgetParam()
    {
        return $this->widgetParam;
    }

    /**
     * set WidgetParam
     *
     * @param WidgetParam $widgetParam
     */
    public function setWidgetParam(WidgetParam $widgetParam)
    {
        $this->widgetParam = $widgetParam;
    }
}
