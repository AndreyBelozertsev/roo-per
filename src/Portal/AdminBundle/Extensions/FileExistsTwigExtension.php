<?php

namespace Portal\AdminBundle\Extensions;

/**
 * Class FileExistsTwigExtension
 * @package Portal\AdminBundle\Extensions
 */
class FileExistsTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'fileExists' => new \Twig_Filter_Method($this, 'fileExists'),
        );
    }

    public function fileExists($file)
    {
        if (file_exists($file)) {
            return true;
        }
        return false;
    }


    public function getName()
    {
        return 'file_exists_extension';
    }
}
