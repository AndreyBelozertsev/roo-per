<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\ArticleCategory;

/**
 * @package Portal\ContentBundle\Widgets
 */
class WidgetCategoryList extends AbstractWidgets
{
    function getList()
    {
        $em = $this->container->get('doctrine')->getManager();
        $result = $em->getRepository('PortalContentBundle:ArticleCategory')->findBy([
            'isPublished' => true,
            'showInMenu'=> true
        ],
        [
            'sort' => 'DESC'
        ]);

        foreach ($result as $cat) {
            /** @var ArticleCategory $cat */
            $id = $cat->getId();
            switch ($this->container->get('request_stack')->getCurrentRequest()->getLocale()) {
                case 'uk':
                    $arrParams[$id] = $cat->getTitleUk();
                    break;
                case 'ru':
                    $arrParams[$id] = $cat->getTitleRu();
                    break;
                case 'en':
                    $arrParams[$id] = $cat->getTitleEn();
                    break;
            }
        }
        
        return $arrParams ?? [];
    }
}
