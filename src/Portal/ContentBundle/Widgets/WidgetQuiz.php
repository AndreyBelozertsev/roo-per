<?php

namespace Portal\ContentBundle\Widgets;

/**
 * Class WidgetQuiz
 * @package Portal\ContentBundle\Widgets
 */
class WidgetQuiz extends AbstractWidgets
{
    function render()
    {
        $arrParams['quiz'] = 'WidgetQuiz';
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:quiz.html.twig', $arrParams)->getContent();
    }
}
