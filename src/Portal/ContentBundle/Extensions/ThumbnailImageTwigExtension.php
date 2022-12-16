<?php

namespace Portal\ContentBundle\Extensions;

use Portal\HelperBundle\Helper\AttachmentImageHelper;

/**
 * Class ThumbnailImageTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ThumbnailImageTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'thumbImage' => new \Twig_Filter_Method($this, 'thumbImageUrl'),
        );
    }

    public function thumbImageUrl($originUrl)
    {
        $thumb = preg_replace("/\.(\w+)$/", '_thumbnail.$1', $originUrl);
        if (AttachmentImageHelper::fileExists($thumb)) {
            return $thumb;
        } else {
            return $originUrl;
        }
    }

    public function getName()
    {
        return 'thumb_image_extension';
    }
}
