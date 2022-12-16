<?php

namespace Portal\ContentBundle\Widgets;

/**
 * Class WidgetArticle
 * @package Portal\ContentBundle\Widgets
 */
class WidgetArticle extends AbstractWidgets
{
    /**
     * Latest news widget
     * @return mixed
     */
    function renderPopularNews()
    {
        return $this->container->get('doctrine')->getRepository('PortalContentBundle:Article')
            ->getPopularArticleList();
    }

    /**
     *
     * @param integer $category
     * @param integer $page
     * @return array
     */
    function renderArticleByCategory($category, $page)
    {
        return $this->container->get('doctrine')->getRepository('PortalContentBundle:Article')
            ->getPaginatedList($category, $page);
    }

    /**
     * @param $category
     * @return integer|mixed
     */
    function renderArticleCount($category)
    {
        return $this->container->get('doctrine')->getRepository('PortalContentBundle:Article')
            ->getArticleCount($category);
    }
}
