<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\MenuNode;


/**
 * Class WidgetArticle
 * @package Portal\ContentBundle\Widgets
 */
class WidgetBreadcrumbs extends AbstractWidgets
{
    function render($id)
    {
        $arr = $this->em->getRepository(MenuNode::class)->getParentItemMenById($id);
        $bradcrumbs = [];
        foreach ($arr as $item) {
            $bradcrumbs[] = $item['label'];
        }

        return $this->container->get('templating')
            ->renderResponse('PortalContentBundle:Widgets:bradcrumbs_structure.html.twig', ['bradcrumbs' => $arr])->getContent();
    }
}
