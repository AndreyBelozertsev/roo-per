<?php

namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetLastShown;

/**
 * Class LastShownTwigExtension
 * @package Portal\AdminBundle\Twig\Extension
 */
class LastShownTwigExtension extends \Twig_Extension
{
    protected $lastShown;

    public function __construct(WidgetLastShown $lastShown)
    {
        $this->lastShown = $lastShown;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['lastShown' => $this->lastShown];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lastShown';
    }
}
