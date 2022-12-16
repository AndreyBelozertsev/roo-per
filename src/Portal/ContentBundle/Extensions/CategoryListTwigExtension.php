<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetCategoryList;

/**
 * @package Portal\ContentBundle\Extensions
 */
class CategoryListTwigExtension extends \Twig_Extension
{
    protected $category_list;

    /**
     * @param WidgetCategoryList $category_list
     */
    public function __construct(WidgetCategoryList $category_list)
    {
        $this->category_list = $category_list;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['category_list' => $this->category_list];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'category_list';
    }
}