<?php

namespace Portal\HelperBundle\Helper;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;

class AttachmentImageHelper
{
    const MAX_IMAGE_WIDTH = 1280;
    const WIDTH_THUMBNAIL_RESOLUTION = 282;

    public static function fileExists($originUrl)
    {
        $file = substr($originUrl, 1);
        return file_exists($file);
    }

    public function cropImage($fullPath, $cropX, $cropY, $cropWidth, $cropHeight)
    {
        $file = substr($fullPath, 1);
        $imagine = new Imagine();
        $image = $imagine->open($file);

        if (!$cropWidth || !$cropHeight) {
            $cropWidth = $image->getSize()->getWidth();
            $cropHeight = $image->getSize()->getHeight();
        }

        $cropSize = new Box($cropWidth, $cropHeight);
        $finalSize = $cropSize;
        if ($cropWidth > self::MAX_IMAGE_WIDTH) {
            $finalSize = $cropSize->widen(self::MAX_IMAGE_WIDTH);
        }

        $image->crop(new Point($cropX, $cropY), $cropSize)
            ->resize($finalSize)
            ->save($file);

        $imagine->open($file)
            ->thumbnail($cropSize->widen(self::WIDTH_THUMBNAIL_RESOLUTION), ImageInterface::THUMBNAIL_INSET)
            ->save(preg_replace("/\.(\w+)$/", '_thumbnail.$1', $file));
    }

    public function resizeImage($file, $maxWidth = self::MAX_IMAGE_WIDTH)
    {
        $imagine = new Imagine();
        $image = $imagine->open($file);

        $imageWidth = $image->getSize()->getWidth();
        $imageHeight = $image->getSize()->getHeight();

        $imageSize = new Box($imageWidth, $imageHeight);
        if ($imageWidth > $maxWidth) {
            $imageSize = $imageSize->widen($maxWidth);
        }

        $image->resize($imageSize)->save($file);
    }
}
