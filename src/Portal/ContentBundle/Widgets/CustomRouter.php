<?php
namespace Portal\ContentBundle\Widgets;

/**
 * Class CustomRouter
 * @package Portal\ContentBundle\Widgets
 */
class CustomRouter extends AbstractWidgets
{
    function routeExists($name)
    {
        $router = $this->container->get('router');
        return (null === $router->getRouteCollection()->get($name)) ? false : true;
    }
}