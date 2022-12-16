<?php

namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetSideMenu;

class SideMenuTwigExtension extends \Twig_Extension
{
    protected $sideMenu;

    public function __construct(WidgetSideMenu $sideMenu)
    {
        $this->sideMenu = $sideMenu;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['sideMenu' => $this->sideMenu];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sideMenu';
    }
}
