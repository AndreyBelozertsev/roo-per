<?php
namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\CustomRouter;

/**
 * Class WorkersFioTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class CustomRouterTwigExtension extends \Twig_Extension
{
    protected $custom_router;

    /**
     * CustomRouterTwigExtension constructor.
     * @param CustomRouter $router
     */
    public function __construct(CustomRouter $router) {
        $this->custom_router = $router;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'custom_router' => $this->custom_router
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'custom_router';
    }
}