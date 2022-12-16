<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetSubscribeForm;

/**
 * Class SubscribeFormTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class SubscribeFormTwigExtension extends \Twig_Extension
{
    protected $subscribe_form;

    /**
     * SubscribeFormTwigExtension constructor.
     * @param WidgetSubscribeForm $subscribe_form
     */
    public function __construct(WidgetSubscribeForm $subscribe_form) {
        $this->subscribe_form = $subscribe_form;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'subscribe_form' => $this->subscribe_form
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'subscribe_form';
    }
}
