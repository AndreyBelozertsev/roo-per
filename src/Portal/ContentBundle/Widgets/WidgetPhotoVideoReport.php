<?php

namespace Portal\ContentBundle\Widgets;

class WidgetPhotoVideoReport extends AbstractWidgets
{
    function render()
    {
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:photo_video_report.html.twig', [
            'photoReportList' => $this->container->get('customer_photo_report_manager')->getPhotoReportList(8),
            'videoReportList' => $this->container->get('customer_video_report_manager')->getVideoReportList(8)
        ])->getContent();
    }
}
