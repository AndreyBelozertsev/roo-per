<?php

namespace Portal\ContentBundle\Widgets;


class WidgetChannel extends AbstractWidgets
{
    function render($idWidget2Panel = 0)
    {
        if ($idWidget2Panel) {
            $widgetParam = $this->container->get('customer_widget_param_manager')->getParamByNameAndId('embed_code', $idWidget2Panel);
        } else {
            $widgetParam = $this->container->get('customer_widget_param_manager')->getParamByName('embed_code');
        }

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:channel.html.twig', [
            'channel' => $widgetParam
        ])->getContent();
    }
}
