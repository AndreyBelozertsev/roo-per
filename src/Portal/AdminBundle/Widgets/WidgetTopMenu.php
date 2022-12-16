<?php

namespace Portal\AdminBundle\Widgets;

use Portal\AdminBundle\Entity\Instance;

/**
 * Class WidgetTopMenu
 * @package Portal\AdminBundle\Widgets
 */
class WidgetTopMenu extends AbstractWidgets
{
    public function render()
    {
        $currentUser = $this->container->get('user_helper')->getCurrentUser();
        $allowedInstances = $this->container->get('user_role_manager')->findRoleToUserBy(['user' => $currentUser->getId()]);
        $showMain = false;
        foreach ($allowedInstances as $k => $inst) {
            $allowedList[$k]['title'] = $inst->getInstance()->getTitle();
            $allowedList[$k]['code'] = $code = $inst->getInstance()->getCode();
            if ($code === Instance::MAIN_SITE_INSTANCE_CODE) {
                $showMain = true;
            }
        }

        return $this->container->get('templating')->renderResponse('PortalAdminBundle:Widgets:topMenu.html.twig', [
            'instanceList' => $allowedList ?? $this->container->get('instance_manager')->getInstanceList(),
            'showMain' => isset($allowedList) ? $showMain : true
        ])->getContent();
    }
}
