<?php

namespace Portal\ContentBundle\Extensions;

/**
 * Class ArticleTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class DateRuTwigExtension extends \Twig_Extension
{
    protected $article;

    public function getFilters()
    {
        return array(
            'date_ru' => new \Twig_Filter_Method($this, 'dateRu'),
        );
    }

    public function dateRu($date)
    {
        $months = [1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $key = $date->format('n');
        return $date->format('d ' . $months[$key] . ' Y');
    }

    public function getName()
    {
        return 'date_ru_extension';
    }
}
