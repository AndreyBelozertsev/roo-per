<?php
namespace Portal\AdminBundle\Extensions;

use Portal\AdminBundle\Widgets\WidgetInstance;

/**
 * Class WorkersFioTwigExtension
 * @package Portal\AdminBundle\Extensions
 */
class InstanceTwigExtension extends \Twig_Extension
{
    protected $instance;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetInstance $instance
     */
    public function __construct(WidgetInstance $instance) {
        $this->instance = $instance;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'instance' => $this->instance
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instance';
    }
}