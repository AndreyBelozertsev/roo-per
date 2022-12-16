<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetCalendarNews;

/**
 * Class CalendarNewsTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class CalendarNewsTwigExtension extends \Twig_Extension
{
    protected $calendarNews;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetCalendarNews $calendarNews
     */
    public function __construct(WidgetCalendarNews $calendarNews) {
        $this->calendarNews = $calendarNews;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'news_calendar' => $this->calendarNews
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'news_calendar';
    }
}
