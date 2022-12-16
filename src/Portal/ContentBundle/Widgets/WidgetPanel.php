<?php

namespace Portal\ContentBundle\Widgets;

/**
 * Class WidgetPanel
 * @package Portal\ContentBundle\Widgets
 */
class WidgetPanel extends AbstractWidgets
{
    function renderPanel($codePanel, $codePageTemplate = "main_page")
    {
        $panelWidgetList = $this->container->get('customer_widget_to_panel_manager')->getListWidgetForPanel($codePanel, $codePageTemplate);

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:widgetPanel.html.twig', [
            'panelWidgetList' => $panelWidgetList,
            'codePanel' => $codePanel
        ])->getContent();
    }
}
