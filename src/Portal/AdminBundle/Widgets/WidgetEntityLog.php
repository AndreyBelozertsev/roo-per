<?php

namespace Portal\AdminBundle\Widgets;


/**
 * Class WidgetEntityLog
 * @package Portal\AdminBundle\Widgets
 */
class WidgetEntityLog extends AbstractWidgets
{
    
    public function renderHistory($id, $instanceCode='main', $entityType=\Portal\HelperBundle\Entity\Log::ENTITY_TYPE_ARTICLE)
    {
        $emLog = $this->container->get('doctrine')->getManager('log');
        return $this->container->get('templating')->renderResponse('PortalAdminBundle:Widgets:entityLog.html.twig', [
            'listChanges' => $emLog->getRepository('PortalHelperBundle:Log')->getMessages($id, $instanceCode, $entityType),
        ])->getContent();
    }
    
    
}
