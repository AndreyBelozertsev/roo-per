<?php

namespace Portal\ContentBundle\Widgets;


class WidgetResource extends AbstractWidgets
{
    function render()
    {

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:resource.html.twig', [
            'resource' => 'Resource'
        ])->getContent();
    }
}