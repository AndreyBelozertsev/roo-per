<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetWeather;

/**
 * Class WeatherTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class WeatherTwigExtension extends \Twig_Extension
{
    protected $weather;

    /**
     * WeatherTwigExtension constructor.
     * @param WidgetWeather $weather
     */
    public function __construct(WidgetWeather $weather) {
        $this->weather = $weather;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'weather' => $this->weather
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'weather';
    }
}
