<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetInstagram;

/**
 * Class InstagramTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class InstagramTwigExtension extends \Twig_Extension
{
    protected $instagram;

    /**
     * InstagramTwigExtension constructor.
     * @param WidgetInstagram $instagram
     */
    public function __construct(WidgetInstagram $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['instagram' => $this->instagram];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instagram';
    }
}
