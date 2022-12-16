<?php

namespace Portal\ContentBundle\Widgets;


class WidgetCalendarNews extends AbstractWidgets
{
    function render()
    {
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:calendarNews.html.twig')
            ->getContent();
    }
}
