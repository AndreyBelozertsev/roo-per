<?php

namespace Portal\AdminBundle\Widgets;

/**
 * Class WidgetPermissions
 * @package Portal\AdminBundle\Widgets
 */
class WidgetPermissions extends AbstractWidgets
{
    public function getAll()
    {
        $currentUser = $this->container->get('user_helper')->getCurrentUser();
//        $instanceCode = $this->container->get('kernel')->getInstance() ?: $this->container->getParameter('instance_code');
        $roles = $this->container->get('user_role_manager')->findRoleToUserBy([
            'user' => $currentUser->getId(),
//            'instance' => $this->container->get('instance_manager')->findOneBy(['code' => $instanceCode])->getId()
        ]);
        if (isset($roles[0])) {
            $roleId = $roles[0]->getRole()->getId();
            $allowedPermissions = $this->container->get('user_role_manager')->getPermissionsByRoleId($roleId);
        }

        return $allowedPermissions ?? [];
    }

    public function getCode() {
        $arr = $this->getAll();

        return empty($arr) ? [] : array_column($arr, 'code');
    }
}
