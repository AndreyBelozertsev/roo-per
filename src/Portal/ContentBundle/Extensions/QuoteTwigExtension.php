<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetQuote;

/**
 * Class QuoteTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class QuoteTwigExtension extends \Twig_Extension
{
    protected $quote;

    /**
     * QuoteTwigExtension constructor.
     * @param WidgetQuote $quote
     */
    public function __construct(WidgetQuote $quote) {
        $this->quote = $quote;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'quote' => $this->quote
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'quote';
    }
}
