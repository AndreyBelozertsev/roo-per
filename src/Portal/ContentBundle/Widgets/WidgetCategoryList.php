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
                    $arrParams[$id]['title'] = $cat->getTitleUk();
                    break;
                case 'ru':
                    $arrParams[$id]['title'] = $cat->getTitleRu();
                    break;
                case 'en':
                    $arrParams[$id]['title'] = $cat->getTitleEn();
                    break;
            }
            $arrParams[$id]['icon'] =  $cat->getIconAttachment()->getPreviewFileUrl();
            $arrParams[$id]['thumbnail'] =  $cat->getThumbnailAttachment()->getPreviewFileUrl();

        }
        
        return $arrParams ?? [];
    }
}
