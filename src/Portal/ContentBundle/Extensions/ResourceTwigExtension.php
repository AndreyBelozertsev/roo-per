<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetResource;

/**
 * Class ResourceTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ResourceTwigExtension extends \Twig_Extension
{
    protected $resource;

    /**
     * ResourceTwigExtension constructor.
     * @param WidgetResource $resource
     */
    public function __construct(WidgetResource $resource) {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return ['resource' => $this->resource];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'resource';
    }
}
