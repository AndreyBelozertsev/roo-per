<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetSystemMode;

/**
 * Class SystemModeTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class SystemModeTwigExtension extends \Twig_Extension
{
    protected $systemMode;

    /**
     * SystemModeTwigExtension constructor.
     * @param WidgetSystemMode $systemMode
     */
    public function __construct(WidgetSystemMode $systemMode) {
        $this->systemMode = $systemMode;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'systemMode' => $this->systemMode
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'systemMode';
    }
}
