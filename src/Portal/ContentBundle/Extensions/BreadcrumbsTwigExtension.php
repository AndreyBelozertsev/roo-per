<?php
namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetBreadcrumbs;

/**
 * Class WorkersFioTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class BreadcrumbsTwigExtension extends \Twig_Extension
{
    protected $breadcrumbs;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetBreadcrumbs $breadcrumbs
     */
    public function __construct(WidgetBreadcrumbs $breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'breadcrumbs' => $this->breadcrumbs
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'breadcrumbs';
    }
}