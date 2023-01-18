<?php

namespace Portal\ContentBundle\Widgets;


/**
 * Class WidgetGallery
 * @package Portal\ContentBundle\Widgets
 */
class WidgetGallery extends AbstractWidgets
{
    function render()
    {
        $photoReportList = $this->container->get('doctrine')->getRepository('PortalContentBundle:PhotoReport')->getAttachmentListPhotoReportById(1);
        return $this->container->get('templating')
            ->renderResponse('PortalContentBundle:Widgets:Gallery.html.twig', ['photoReportList' => $photoReportList])->getContent();
    }

}
