<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetFollowUs;

/**
 * Class FollowUsTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class FollowUsTwigExtension extends \Twig_Extension
{
    protected $followUs;

    /**
     * FollowUsTwigExtension constructor.
     * @param WidgetFollowUs $followUs
     */
    public function __construct(WidgetFollowUs $followUs) {
        $this->followUs = $followUs;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return ['follow_us' => $this->followUs];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'follow_us';
    }
}
