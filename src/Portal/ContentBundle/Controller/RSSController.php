<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\Option;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RSSController extends Controller
{
    public function indexAction()
    {
        $days = (integer)$this->container->get('option_manager')
            ->findOneBy(['name' => Option::OPTION_START_NAME.Option::OPTION_COMMON.'.'.Option::OPTION_RSS_LAST_DAYS_PARAM_NAME])->getValue();
        return $this->render('PortalContentBundle:RSS:rss.xml.twig', [
            'articles' => $this->container->get('customer_article_manager')->getRssArticles($days)
        ]);
    }
}
