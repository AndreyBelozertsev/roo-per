<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetShare;

/**
 * Class ShareTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ShareTwigExtension extends \Twig_Extension
{
    protected $share;

    /**
     * ShareTwigExtension constructor.
     * @param WidgetShare $share
     */
    public function __construct(WidgetShare $share) {
        $this->share = $share;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return ['share' => $this->share];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'share';
    }
}
