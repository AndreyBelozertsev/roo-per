<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetGallery;

/**
 * Class GalleryTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class GalleryTwigExtension extends \Twig_Extension
{
    protected $gallery;

    /**
     * ArticleTwigExtension constructor.
     * @param WidgetGallery $gallery
     */
    public function __construct(WidgetGallery $gallery) {
        $this->gallery = $gallery;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'galleryWidget' => $this->gallery
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'galleryWidget';
    }
}
