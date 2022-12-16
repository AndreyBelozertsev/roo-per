<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\SystemMode;

/**
 * Class WidgetSystemMode
 * @package Portal\ContentBundle\Widgets
 */
class WidgetSystemMode extends AbstractWidgets
{
    function serviceMode()
    {
        $arrParams = [];
        $widgetSystemMode = $this->container->get('system_mode_manager')->getSystemModeByCode(SystemMode::SERVICE_MODE);
        $arrParams['widgetSystemMode'] = $widgetSystemMode;
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:systemMode.html.twig', $arrParams)->getContent();
    }
}
