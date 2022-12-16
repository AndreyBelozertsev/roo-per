<?php

namespace Portal\ContentBundle\Extensions;

use Portal\HelperBundle\Helper\PortalHelper;

/**
 * Class PluralizeTwigExtension
 * @package Portal\ContentBundle\Twig\Extension
 */
class PluralizeTwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            'pluralize' => new \Twig_Filter_Method($this, 'pluralize'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function pluralize($val, $string)
    {
        return PortalHelper::pluralize($val, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pluralize_twig';
    }
}
