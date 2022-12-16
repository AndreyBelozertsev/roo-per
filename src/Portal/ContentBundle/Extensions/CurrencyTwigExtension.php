<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetCurrency;

/**
 * Class CurrencyTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class CurrencyTwigExtension extends \Twig_Extension
{
    protected $currency;

    /**
     * CurrencyTwigExtension constructor.
     * @param WidgetCurrency $currency
     */
    public function __construct(WidgetCurrency $currency) {
        $this->currency = $currency;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'currency' => $this->currency
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'currency';
    }
}
