<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetPanel;

/**
 * Class PanelWidgetTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class PanelWidgetTwigExtension extends \Twig_Extension
{
    protected $panelWidget;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetPanel $panelWidget
     */
    public function __construct(WidgetPanel $panelWidget) {
        $this->panelWidget = $panelWidget;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'panelWidget' => $this->panelWidget
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'panelWidget';
    }
}
