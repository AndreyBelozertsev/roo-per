<?php

namespace Portal\ContentBundle\Extensions;
use Portal\HelperBundle\Helper\PortalHelper;

/**
 * Class SliceTitleTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class SliceFullWordTwigExtension extends \Twig_Extension
{
    protected $article;

    public function getFilters()
    {
        return [
            'sliceFullWord' => new \Twig_Filter_Method($this, 'sliceTitle'),
        ];
    }

    public function sliceTitle($str, $len, $dots = false)
    {
        return PortalHelper::sliceFullWord($str, $len) . (($dots && mb_strlen($str) > $len ) ? '...' : '');
    }

    public function getName()
    {
        return 'slice_full_word_extension';
    }
}
