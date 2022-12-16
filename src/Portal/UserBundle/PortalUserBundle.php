<?php

namespace Portal\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Portal\UserBundle\DependencyInjection\Security\Factory\WsseFactory;

class PortalUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
    
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }
}
