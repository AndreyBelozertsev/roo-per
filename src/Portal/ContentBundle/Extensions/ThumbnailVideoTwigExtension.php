<?php

namespace Portal\ContentBundle\Extensions;

use Portal\HelperBundle\Helper\AttachmentImageHelper;

/**
 * Class ThumbnailVideoTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class ThumbnailVideoTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'thumbVideo' => new \Twig_Filter_Method($this, 'thumbVideoUrl'),
        );
    }

    public function thumbVideoUrl($originUrl)
    {
        $thumb = preg_replace("/\.\w+$/", '.thumb.png', $originUrl);
        if (AttachmentImageHelper::fileExists($thumb)) {
            return $thumb;
        } else {
            return 'bundles/portalcontent/image/video.png';
        }
    }

    public function getName()
    {
        return 'thumb_video_extension';
    }
}
