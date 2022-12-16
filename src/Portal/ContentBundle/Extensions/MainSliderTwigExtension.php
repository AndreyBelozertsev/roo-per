<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetMainSlider;

/**
 * Class MainSliderTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class MainSliderTwigExtension extends \Twig_Extension
{
    protected $slider;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetMainSlider $slider
     */
    public function __construct(WidgetMainSlider $slider) {
        $this->slider = $slider;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'main_slider' => $this->slider
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_slider';
    }
}