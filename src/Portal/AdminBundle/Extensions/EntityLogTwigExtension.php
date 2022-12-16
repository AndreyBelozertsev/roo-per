<?php

namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetEntityLog;

/**
 * Class EntityLogTwigExtension
 * @package Portal\AdminBundle\Twig\Extension
 */
class EntityLogTwigExtension extends \Twig_Extension
{
    protected $entityLog;

    public function __construct(WidgetEntityLog $entityLog)
    {
        $this->entityLog = $entityLog;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['entityLog' => $this->entityLog];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'entityLog';
    }
}
