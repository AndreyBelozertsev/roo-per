<?php

namespace Portal\ContentBundle\Extensions;

use Portal\HelperBundle\Helper\AttachmentImageHelper;

/**
 * Class FileImageExistsTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class FileImageExistsTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'existsImage' => new \Twig_Filter_Method($this, 'fileImageExists'),
        );
    }

    public function fileImageExists($originUrl)
    {
        if ($originUrl) {
            if (AttachmentImageHelper::fileExists($originUrl)) {
                return $originUrl;
            }
        }
        return '/bundles/portalcontent/image/image.png';
    }


    public function getName()
    {
        return 'exists_image_extension';
    }
}
