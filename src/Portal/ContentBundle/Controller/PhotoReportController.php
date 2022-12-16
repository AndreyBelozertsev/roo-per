<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\PhotoReport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class PhotoReportController extends Controller
{
    public function photoListAction()
    {
        $arrParams['isDepartment'] = false;

        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $arrParams['photoReportCount'] = $this->get('customer_photo_report_manager')->getPhotoReportListCount();
        $arrParams['photoReportList'] = $this->get('customer_photo_report_manager')->getPhotoReportList(PhotoReport::PHOTO_REPORT_LIMIT_ON_PAGE);

        return $this->render('PortalContentBundle:PhotoReport:photo_list.html.twig', $arrParams);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function showPhotoReportAction(Request $request, $id)
    {
        $objPhotoReport = $this->get('customer_photo_report_manager')->findOneById($id);
        if (!$objPhotoReport instanceof PhotoReport || $objPhotoReport->getIsDeleted()) {
            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $arrParams['photoReport'] = $objPhotoReport;
        $arrParams['photoList'] = $this->get('customer_photo_report_manager')->getAttachmentListById($id);

        $photoreport_shown_ids = explode(',', $request->cookies->get('photoreport_shown_ids'));
        if (!in_array($id, $photoreport_shown_ids)) {
            // increment real counter
            $objPhotoReport->setViewsCounter((int)$objPhotoReport->getViewsCounter() + 1);

            $em = $this->getDoctrine()->getManager('customer');
            $em->persist($objPhotoReport);
            $em->flush();
            $photoreport_shown_ids[] = $id;
        }

        $response = $this->render('PortalContentBundle:PhotoReport:show_photo.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('photoreport_shown_ids', implode(',', $photoreport_shown_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    public function getNextPhotoReportAction(Request $request)
    {
        $photoReportList = $this->get('customer_photo_report_manager')->getPhotoReportList(
            PhotoReport::PHOTO_REPORT_LIMIT_ON_PAGE,
            (int)$request->get('shown')
        );
        if (false !== $photoReportList) {
            $arrJson['listCount'] = sizeof($photoReportList);
            $arrParams['photoReportList'] = $photoReportList;
        } else {
            $arrParams['message'] = $this->get('translator')->trans('no_records');
        }
        $arrJson['content'] = $this->render(
            'PortalContentBundle:PhotoReport:show_next_photo.html.twig',
            $arrParams ?? []
        )->getContent();

        return new JsonResponse($arrJson);
    }
}
