<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Portal\AdminBundle\Form\PhotoReportType;
use Portal\ContentBundle\Entity\PhotoReport;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class PhotoReportAdminController extends Controller
{
    public function viewAllAction(Request $request, $instanceCode)
    {
        $adapter = new ArrayAdapter($this->get('customer_photo_report_manager')->getAllPhotoReport());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:PhotoReportAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        if ($id == 0) {
            // create
            $photoReport = new PhotoReport();
            $permissionCode = 'create_photo_report';
            $validation_group = ['new'];
            if ($request->query->get('menuNodeId')) {
                $photoReport->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
            }
        } else {
            // edit
            $photoReport = $this->get('customer_photo_report_manager')->findOneById($id);
            if (!$photoReport instanceof PhotoReport || $photoReport->getIsDeleted()) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = 'edit_photo_report';
            $validation_group = ['edit'];
        }
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            $permissionCode,
            $instanceCode,
            $currentUserId
        );
        $isAuthor = ($photoReport->getAuthor() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(PhotoReportType::class, $photoReport, [
//            'is_published' => $photoReport->getIsPublished(),
            'validation_groups' => $validation_group,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $photoReport->setAuthor($currentUserId);
                    $photoReport->setIsSearchIndexed(FALSE);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($photoReport);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    if ($request->isXmlHttpRequest()) {

                        return new JsonResponse([
                            'status' => true,
                            'route' => $this->generateUrl('admin_instance_photo_report_edit', [
                                'id' => $photoReport->getId(),
                                'instanceCode' => $instanceCode
                            ])
                        ]);
                    } else {

                        return $this->redirectToRoute('admin_instance_photo_report_edit', [
                            'id' => $photoReport->getId(),
                            'instanceCode' => $instanceCode
                        ]);
                    }
                } catch (DBALException $e) {
                    $errMessage = $this->get('translator')->trans('query_error');
                    if ($request->isXmlHttpRequest()) {

                        return new JsonResponse([
                            'status' => false,
                            'errors' => $errMessage
                        ]);
                    } else {
                        $flashBag->add('error_message', $errMessage);
                    }
                }
            } else {
                if ($request->isXmlHttpRequest()) {
                    $errors = [];
                    foreach ($form->getErrors(true) as $k => $err) {
                        $errors[$k]['field'] = $err->getCause()->getPropertyPath();
                        $file = $err->getCause()->getInvalidValue();
                        if ($file instanceof UploadedFile) {
                            $errors[$k]['file'] = $file->getClientOriginalName();
                        }
                        $errors[$k]['message'] = $err->getMessage();
                    }

                    return new JsonResponse([
                        'status' => false,
                        'errors' => $errors
                    ]);
                } else {
                    $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
                }
            }
        }

        return $this->render('PortalAdminBundle:PhotoReportAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'photoReport' => $photoReport,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction(Request $request, $id, $instanceCode)
    {
        $photoReport = $this->get('customer_photo_report_manager')->findOneById($id);
        if ($photoReport instanceof PhotoReport) {
            $isGranted = $this->get('user_manager')->isGranted(
                'delete_photo_report',
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $photoReport->setIsDeleted(true);
                $photoReport->setIsSearchIndexed(FALSE);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($photoReport);
                $em->flush();

                if ($request->query->get('page') !== null) {
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_photo_report_view_all', [
                        'instanceCode' => $instanceCode,
                        'page' => (int)$request->query->get('page') ?: null
                    ]);
                } else {
                    $response['reload'] = true;
                }
                $response['status'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function restoreAction($id, $instanceCode)
    {
        $photoReport = $this->get('customer_photo_report_manager')->findOneById($id);
        if ($photoReport instanceof PhotoReport) {
            $isGranted = $this->get('user_manager')->isGranted(
                'restore_photo_report',
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $photoReport->setIsDeleted(false);
                $photoReport->setIsSearchIndexed(FALSE);
                $em->persist($photoReport);
                $em->flush();

                $response['status'] = true;
                $response['reload'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
