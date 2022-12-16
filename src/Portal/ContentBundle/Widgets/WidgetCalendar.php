<?php

namespace Portal\ContentBundle\Widgets;


class WidgetCalendar extends AbstractWidgets
{
    function render()
    {
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:calendar.html.twig')
            ->getContent();
    }
}
