<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\VideoReport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class VideoReportController extends Controller
{
    public function videoListAction()
    {
        $arrParams['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $arrParams['videoReportCount'] = $this->get('customer_video_report_manager')->getVideoReportListCount();
        $arrParams['videoReportList'] = $this->get('customer_video_report_manager')->getVideoReportList(VideoReport::VIDEO_REPORT_LIMIT_ON_PAGE);

        return $this->render('PortalContentBundle:VideoReport:video_list.html.twig', $arrParams);
    }

    public function showVideoAction(Request $request, $id)
    {
        $arrParams['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
//            $arrParams['depCode'] = $instanceCode;
            $arrParams['instanceCode'] = $instanceCode;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        $videoReport = $this->container->get('customer_video_report_manager')->findOneById($id);
        if (!$videoReport instanceof VideoReport) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        $arrParams['videoReport'] = $videoReport;
        $arrParams['tags'] = $this->get('customer_tag_manager')->getTagsByVideoReportId($id);

        $videoreport_shown_ids = explode(',', $request->cookies->get('videoreport_shown_ids'));
        if (!in_array($id, $videoreport_shown_ids)) {
            // increment real counter
            $videoReport->setViewsCounter((int)$videoReport->getViewsCounter() + 1);

            $em = $this->getDoctrine()->getManager('customer');
            $em->persist($videoReport);
            $em->flush();
            $videoreport_shown_ids[] = $id;
        }
        
        $response = $this->render('PortalContentBundle:VideoReport:show_video.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('videoreport_shown_ids', implode(',', $videoreport_shown_ids), strtotime('now + 3 months'))
        );
                
        return $response;
    }

    public function getNextVideoReportAction(Request $request)
    {
        $videoReportList = $this->get('customer_video_report_manager')->getVideoReportList(
            VideoReport::VIDEO_REPORT_LIMIT_ON_PAGE,
            (int)$request->get('shown')
        );
        if (false !== $videoReportList) {
            $arrJson['listCount'] = sizeof($videoReportList);
            $arrParams['videoReportList'] = $videoReportList;
        } else {
            $arrParams['message'] = $this->get('translator')->trans('no_records');
        }
        $arrJson['content'] = $this->render(
            'PortalContentBundle:VideoReport:show_next_video.html.twig',
            $arrParams ?? []
        )->getContent();

        return new JsonResponse($arrJson);
    }
}
