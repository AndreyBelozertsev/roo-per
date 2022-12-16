<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetPortalHelper;

/**
 * Class WorkersFioTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class WidgetPortalHelperTwigExtension extends \Twig_Extension
{
    protected $widgetPoptalHelper;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetPortalHelper $widgetPoptalHelper
     */
    public function __construct(WidgetPortalHelper $widgetPoptalHelper) {
        $this->widgetPoptalHelper = $widgetPoptalHelper;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'portal_helper_widget' => $this->widgetPoptalHelper
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'portal_helper_widget';
    }
}