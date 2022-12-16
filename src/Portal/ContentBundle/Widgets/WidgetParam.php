<?php

namespace Portal\ContentBundle\Widgets;

class WidgetParam extends AbstractWidgets
{
    function getParams()
    {
        $records = $this->container->get('doctrine')->getRepository('PortalContentBundle:Param')
            ->findAll();

        return array_column($records, 'value', 'name');
    }
}
