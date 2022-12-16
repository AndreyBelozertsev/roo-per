<?php

namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetPermissions;

/**
 * Class PermissionsTwigExtension
 * @package Portal\AdminBundle\Extensions
 */
class PermissionsTwigExtension extends \Twig_Extension
{
    protected $permissions;

    public function __construct(WidgetPermissions $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['permissions' => $this->permissions];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'permissions';
    }
}
