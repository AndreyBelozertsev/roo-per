<?php

namespace Portal\ContentBundle\Extensions;

/**
 * Class ArticleTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class StripTwigExtension extends \Twig_Extension
{
    protected $article;

    public function getFilters()
    {
        return array(
            'strip' => new \Twig_Filter_Method($this, 'stripSlashes'),
        );
    }

    public function stripSlashes($str)
    {
        return stripslashes($str);
    }

    public function getName()
    {
        return 'strip_extension';
    }
}
