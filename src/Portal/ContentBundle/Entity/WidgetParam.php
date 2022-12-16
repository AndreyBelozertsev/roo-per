<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WidgetParam
 *
 * @ORM\Table(name="widget_param")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\WidgetParamRepository")
 */
class WidgetParam
{
    const WIDGET_PARAM_TEXT_TYPE = 0;
    const WIDGET_PARAM_SELECT_TYPE = 1;

    const WIDGET_FOLLOW_US_PARAM_LIST = [
        'vk_group_link' => ['css' => 'icon_vk-blue', 'css-grey' => 'icon_vk', 'name' => 'ВКонтакте', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'fb_group_link' => ['css' => 'icon_fb-blue', 'css-grey' => 'icon_fb', 'name' => 'Facebook', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'tw_group_link' => ['css' => 'icon_tw-blue', 'css-grey' => 'icon_tw', 'name' => 'Twitter', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'ig_group_link' => ['css' => 'icon_ig-blue', 'css-grey' => 'icon_ig', 'name' => 'Instagram', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'ok_group_link' => ['css' => 'icon_ok-blue', 'css-grey' => 'icon_ok', 'name' => 'Одноклассники', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'lj_group_link' => ['css' => 'icon_lj-blue', 'css-grey' => 'icon_lj', 'name' => 'LiveJournal', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'yt_group_link' => ['css' => 'icon_yt-blue', 'css-grey' => 'icon_yt', 'name' => 'YouTube', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'color' => ['name' => 'Цвет', 'type' => self::WIDGET_PARAM_SELECT_TYPE, 'options' => ['default' => 'По умолчанию (синий)', 'grey' => 'Серый']]
    ];

    const WIDGET_CHANNEL_PARAM_LIST = [
        'embed_code' => ['name' => 'embed_code', 'type' => self::WIDGET_PARAM_TEXT_TYPE]
    ];

    const WIDGET_QUOTE_PARAM_LIST = [
        'quote' => ['name' => 'Текст цитаты', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'quote_label' => ['name' => 'Заголовок', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'quote_link' => ['name' => 'Ссылка', 'type' => self::WIDGET_PARAM_TEXT_TYPE]
    ];

    const WIDGET_SLIDER_PARAM_LIST = [
        'slider' => ['name' => 'Слайдер из списка', 'type' => self::WIDGET_PARAM_SELECT_TYPE]
    ];

    const WIDGET_INSTAGRAM_PARAM_LIST = [
        'title' => ['name' => 'Заголовок виджета', 'type' => self::WIDGET_PARAM_TEXT_TYPE],
        'account_name' => ['name' => 'Имя аккаунта instagram', 'type' => self::WIDGET_PARAM_TEXT_TYPE]
    ];

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
     * @ORM\Column(name="param_name", type="string", length=100)
     */
    private $paramName;

    /**
     * @var string
     *
     * @ORM\Column(name="param_value", type="text")
     */
    private $paramValue;

    /**
     * @var string
     *
     * @ORM\Column(name="param_title", type="string", length=255, nullable=true)
     */
    private $paramTitle;

    /**
     * @var WidgetToPanel
     *
     * @ORM\ManyToOne(targetEntity="Portal\ContentBundle\Entity\WidgetToPanel", inversedBy="widgetParam")
     * @ORM\JoinColumn(name="widget2panel_id", referencedColumnName="id")
     */
    private $widgetToPanelId;

    /**
     * @var string
     *
     * @ORM\Column(name="param_type", type="integer", nullable=false, options={"default":0})
     */
    private $paramType = 0;

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
     * Set paramName
     *
     * @param string $paramName
     *
     * @return WidgetParam
     */
    public function setParamName($paramName)
    {
        $this->paramName = $paramName;

        return $this;
    }

    /**
     * Get paramName
     *
     * @return string
     */
    public function getParamName()
    {
        return $this->paramName;
    }

    /**
     * Set paramValue
     *
     * @param string $paramValue
     *
     * @return WidgetParam
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;

        return $this;
    }

    /**
     * Get paramValue
     *
     * @return string
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }

    /**
     * Set paramTitle
     *
     * @param string $paramTitle
     *
     * @return WidgetParam
     */
    public function setParamTitle($paramTitle)
    {
        $this->paramTitle = $paramTitle;

        return $this;
    }

    /**
     * Get paramTitle
     *
     * @return string
     */
    public function getParamTitle()
    {
        return $this->paramTitle;
    }

    /**
     * Set widgetToPanel
     *
     * @param WidgetToPanel $widgetId
     *
     * @return WidgetParam
     */
    public function setWidgetToPanelId($widgetId)
    {
        $this->widgetToPanelId = $widgetId;

        return $this;
    }

    /**
     * Get widgetToPanel
     *
     * @return WidgetToPanel
     */
    public function getWidgetToPanelId()
    {
        return $this->widgetToPanelId;
    }

    /**
     * Set paramType
     *
     * @param string $paramType
     *
     * @return WidgetParam
     */
    public function setParamType($paramType)
    {
        $this->paramType = $paramType;

        return $this;
    }

    /**
     * Get paramType
     *
     * @return string
     */
    public function getParamType()
    {
        return $this->paramType;
    }
}
