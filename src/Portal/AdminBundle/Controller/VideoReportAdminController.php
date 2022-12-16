<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\VideoReportType;
use Portal\ContentBundle\Entity\VideoReport;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class VideoReportAdminController extends Controller
{
    public function viewAllAction(Request $request, $instanceCode)
    {
        $adapter = new ArrayAdapter($this->get('customer_video_report_manager')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:VideoReportAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        if ($id == 0) {
            // create
            $videoReport = new VideoReport();
            $permissionCode = 'create_video_report';
            $validation_group = ['new'];
            if ($request->query->get('menuNodeId')) {
                $videoReport->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
            }
        } else {
            // edit
            $videoReport = $this->get('customer_video_report_manager')->findOneById($id);
            if (!$videoReport instanceof VideoReport) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = 'edit_video_report';
            $validation_group = ['edit'];
        }
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            $permissionCode,
            $instanceCode,
            $currentUserId
        );
        $isAuthor = ($videoReport->getAuthor() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(VideoReportType::class, $videoReport, [
            'validation_groups' => $validation_group
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $videoReport->setAuthor($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($videoReport);
                    $em->flush();

                    // make thumb for video
                    if ($videoThumb = $request->request->get('video_thumb')) {
                        $img = str_replace('data:image/png;base64,', '', $videoThumb);
                        $img = str_replace(' ', '+', $img);
                        $videoPath = $videoReport->getAttachment()->getPreviewFileUrl();
                        $ext = pathinfo($videoPath, PATHINFO_EXTENSION);
                        $thumbPath = $this->get('kernel')->getProjectDir() . '/web' . str_replace($ext, 'thumb.png', $videoPath);
                        file_put_contents($thumbPath, base64_decode($img));
                        // validate thumb
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $fileType = finfo_file($finfo, $thumbPath);
                        finfo_close($finfo);
                        if ($fileType != 'image/png') {
                            unlink($thumbPath);
                        }
                    }

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_video_report_edit', [
                        'id' => $videoReport->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }
        $thumbPath = '';
        if (($attach = $videoReport->getAttachment()) !== null) {
            $videoPath = $attach->getPreviewFileUrl();
            $ext = pathinfo($videoPath, PATHINFO_EXTENSION);
            $thumbPath = str_replace($ext, 'thumb.png', $videoPath);
        }

        return $this->render('PortalAdminBundle:VideoReportAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'videoReport' => $videoReport,
            'thumb' => $thumbPath,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $videoReport = $this->get('customer_video_report_manager')->findOneById($id);
        if ($videoReport instanceof VideoReport) {
            $isGranted = $this->get('user_manager')->isGranted(
                'delete_video_report',
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($videoReport);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_video_report_view_all', [
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
