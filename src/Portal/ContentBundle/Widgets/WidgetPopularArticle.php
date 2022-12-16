<?php

namespace Portal\ContentBundle\Widgets;

/**
 * Class WidgetPopularArticle
 * @package Portal\ContentBundle\Widgets
 */
class WidgetPopularArticle extends AbstractWidgets
{
    function render()
    {
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:popular_articles.html.twig', [
            'popularArticleList' => $this->container->get('customer_article_manager')->getPopularArticleList(),
        ])->getContent();
    }
}
