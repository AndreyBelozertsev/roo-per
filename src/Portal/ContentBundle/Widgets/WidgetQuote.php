<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\InstanceCategory;
use Symfony\Component\BrowserKit\Response;

/**
 * Class WidgetQuote
 * @package Portal\ContentBundle\Widgets
 */
class WidgetQuote extends AbstractWidgets
{
    function render($idWidget2Panel)
    {
        $widgetParamQuote = $this->container->get('customer_widget_param_manager')->getParamByNameAndId('quote', $idWidget2Panel);
        $widgetParamQuoteLabel = $this->container->get('customer_widget_param_manager')->getParamByNameAndId('quote_label', $idWidget2Panel);
        $widgetParamQuoteLink = $this->container->get('customer_widget_param_manager')->getParamByNameAndId('quote_link', $idWidget2Panel);
        $arrParams['quote'] = $widgetParamQuote;
        $arrParams['quote_label'] = $widgetParamQuoteLabel;
        $arrParams['quote_link'] = $widgetParamQuoteLink;
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:quote.html.twig', $arrParams)->getContent();
    }
}
