<?php

namespace Portal\ContentBundle\Widgets;


class WidgetShare extends AbstractWidgets
{
    function render($image = null, $title = '')
    {
        return $this->container->get('templating')
            ->renderResponse('PortalContentBundle:Widgets:share.html.twig', ['image' => $image, 'title' => $title])
            ->getContent();
    }
}
