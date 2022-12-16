<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\WidgetParam;

class WidgetFollowUs extends AbstractWidgets
{
    function render($idWidget2Panel = 0)
    {
        $arrParamNames = array_keys(WidgetParam::WIDGET_FOLLOW_US_PARAM_LIST);
        $widgetParams = $this->container->get('customer_widget_param_manager')->getNotEmptyParamsByNamesAndId($arrParamNames, $idWidget2Panel);
        
        $param = '';
        foreach ($widgetParams as $k => &$v) {
            if ($v['param_name'] == 'color') {
                if ($v['param_value'] == 'grey') {
                    $param = 'grey';
                }
                unset($widgetParams[$k]);
                continue;
            }
            $v['css'] = WidgetParam::WIDGET_FOLLOW_US_PARAM_LIST[$v['param_name']]['css'];
            $v['css_grey'] = WidgetParam::WIDGET_FOLLOW_US_PARAM_LIST[$v['param_name']]['css-grey'];
        }
//        $param = 'grey';
        $twig = ($param === 'grey') ?
            'PortalContentBundle:Widgets:follow_us_grey.html.twig' :
            'PortalContentBundle:Widgets:follow_us.html.twig';
//        $twig = 'PortalContentBundle:Widgets:follow_us.html.twig';
        return $this->container->get('templating')->renderResponse($twig, ['socials' => $widgetParams])->getContent();
    }
}
