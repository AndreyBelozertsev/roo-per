<?php

namespace Portal\ContentBundle\Widgets;

use Portal\HelperBundle\Helper\PortalHelper;

/**
 * Class Slider
 * @package Portal\ContentBundle\Widgets
 */
class WidgetPortalHelper extends AbstractWidgets
{
    function getPathOfTypeAttachment($type, $instance = '')
    {
        return PortalHelper::getAttachmentPath($type, $instance);
    }

}