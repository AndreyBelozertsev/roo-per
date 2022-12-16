<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetCalendar;

/**
 * Class CalendarTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class CalendarTwigExtension extends \Twig_Extension
{
    protected $calendar;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetCalendar $calendar
     */
    public function __construct(WidgetCalendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['calendar' => $this->calendar];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'calendar_twig';
    }
}
