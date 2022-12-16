<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetPhotoVideoReport;

/**
 * Class PhotoVideoTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class PhotoVideoTwigExtension extends \Twig_Extension
{
    protected $photoVideoReport;

    /**
     * PhotoVideoTwigExtension constructor.
     * @param WidgetPhotoVideoReport $photoVideoReport
     */
    public function __construct(WidgetPhotoVideoReport $photoVideoReport) {
        $this->photoVideoReport = $photoVideoReport;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'photoVideoReport' => $this->photoVideoReport
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'photoVideoReport';
    }
}
