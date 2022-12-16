<?php

namespace Portal\AdminBundle\Widgets;

use Portal\UserBundle\Entity\User;

/**
 * Class WidgetSideMenu
 * @package Portal\AdminBundle\Widgets
 */
class WidgetSideMenu extends AbstractWidgets
{
    public function render()
    {
        $container = $this->container;
        if ($container->get('security.authorization_checker')->isGranted(User::SUPER_ADMIN_ROLE)) {
            $superAdmin = true;
        } else {
            $superAdmin = false;
            $allowedPermissions = $container->get('permissions')->getAll();
        }

        try {
            return $container->get('templating')->renderResponse('PortalAdminBundle:Widgets:sideMenu.html.twig', [
                'sa' => $superAdmin,
                'permissions' => isset($allowedPermissions) ? array_column($allowedPermissions, 'code') : [],
                'category_list' =>  $container->get('doctrine')->getRepository('PortalContentBundle:ArticleCategory')->findAll()
            ])->getContent();
        } catch (\Twig_Error $e) {
            return $e->getMessage();
        }
    }
}
