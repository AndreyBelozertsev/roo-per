<?php

namespace Portal\AdminBundle\Extensions;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class JsonDecodeTwigExtension
 * @package Portal\AdminBundle\Twig\Extension
 */
class JsonDecodeTwigExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'json_decode.extension';
    }

    public function getFilters() {
        return array(
            'json_decode'   => new \Twig_Filter_Method($this, 'jsonDecode'),
        );
    }

    public function jsonDecode($str) {
        return json_decode($str);
    }
}
