<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetChannel;

/**
 * Class ChannelTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ChannelTwigExtension extends \Twig_Extension
{
    protected $channel;

    /**
     * ChannelTwigExtension constructor.
     * @param WidgetChannel $channel
     */
    public function __construct(WidgetChannel $channel) {
        $this->channel = $channel;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return ['channel' => $this->channel];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'channel';
    }
}
