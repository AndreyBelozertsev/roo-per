<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetParam;

class ParamTwigExtension extends \Twig_Extension
{
    protected $param;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetParam $param
     */
    public function __construct(WidgetParam $param) {
        $this->param = $param;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return ['paramWidget' => $this->param];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'paramWidget';
    }
}
