<?php

namespace Portal\AdminBundle\Widgets;


/**
 * Class WidgetLastShown
 * @package Portal\AdminBundle\Widgets
 */
class WidgetLastShown extends AbstractWidgets
{
    public function urlList()
    {
        $userId = $this->container->get('user_helper')->getCurrentUser()->getId();

        return $this->container->get('access_log_manager')->getLastUrls($userId);
    }
}
