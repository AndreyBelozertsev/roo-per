<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetArticle;

/**
 * Class ArticleTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ArticleTwigExtension extends \Twig_Extension
{
    protected $article;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetArticle $article
     */
    public function __construct(WidgetArticle $article) {
        $this->article = $article;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'articleWidget' => $this->article
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'articleWidget';
    }
}
