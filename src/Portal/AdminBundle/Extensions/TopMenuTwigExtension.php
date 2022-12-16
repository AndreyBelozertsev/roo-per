<?php

namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetTopMenu;

class TopMenuTwigExtension extends \Twig_Extension
{
    protected $topMenu;

    public function __construct(WidgetTopMenu $topMenu)
    {
        $this->topMenu = $topMenu;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['topMenu' => $this->topMenu];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'topMenu';
    }
}
