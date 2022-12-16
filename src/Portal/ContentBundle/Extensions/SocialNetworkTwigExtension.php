<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetSocialNetwork;

/**
 * Class ArticleTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class SocialNetworkTwigExtension extends \Twig_Extension
{
    protected $socialNetwork;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetSocialNetwork $socialNetwork
     */
    public function __construct(WidgetSocialNetwork $socialNetwork) {
        $this->socialNetwork = $socialNetwork;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'socialNetworkWidget' => $this->socialNetwork
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'socialNetworkWidget';
    }
}
