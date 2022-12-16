<?php
namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetMenu;

/**
 * Class WorkersFioTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class MenuTwigExtension extends \Twig_Extension
{
    protected $menu;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetMenu $menu
     */
    public function __construct(WidgetMenu $menu) {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'menu' => $this->menu
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}