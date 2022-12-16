<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\SocialNetwork;


/**
 * Class WidgetSocialNetwork
 * @package Portal\ContentBundle\Widgets
 */
class WidgetSocialNetwork extends AbstractWidgets
{
    function render()
    {
        $socialNetworks = $this->em->getRepository(SocialNetwork::class)->findBy(['isPublished' => true], ['sort' => 'ASC']);
        return $this->container->get('templating')
            ->renderResponse('PortalContentBundle:Widgets:socialNetworks.html.twig', ['socialNetworks' => $socialNetworks])->getContent();
    }

    function renderBottom()
    {
        $socialNetworks = $this->em->getRepository(SocialNetwork::class)->findBy(['isPublished' => true], ['sort' => 'ASC']);
        return $this->container->get('templating')
            ->renderResponse('PortalContentBundle:Widgets:socialNetworksBottom.html.twig', ['socialNetworks' => $socialNetworks])->getContent();
    }
}
