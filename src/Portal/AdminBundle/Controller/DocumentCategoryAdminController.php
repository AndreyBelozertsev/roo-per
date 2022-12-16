<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\AdminBundle\Form\DocumentCategoryFormType;
use Portal\ContentBundle\Entity\DocumentCategory;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DocumentCategoryAdminController extends Controller
{
    public function viewAllAction(Request $request, $instanceCode)
    {
        $adapter = new ArrayAdapter($this->get('customer_document_category_manager')->getAllDocumentCategory());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(DocumentCategory::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:DocumentCategoryAdmin:viewAll.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
        ]);
    }

    public function editAction(Request $request, $documentCategoryId, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($documentCategoryId == 0) {
            // create
            $docCategory = new DocumentCategory();
            $permissionCode = 'create_document_category';
            $validation_group = ['new'];
        } else {
            // edit
            $docCategory = $this->get('customer_document_category_manager')->findOneById($documentCategoryId);
            if (!$docCategory instanceof DocumentCategory) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = 'edit_document_category';
            $validation_group = ['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(DocumentCategoryFormType::class, $docCategory, [
                'validation_groups' => $validation_group,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $docCategory->setSlug(PortalHelper::slug($docCategory->getTitle()));
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($docCategory);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_document_category_view_all', [
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:DocumentCategoryAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($documentCategoryId, $instanceCode)
    {
        $docCategory = $this->get('customer_document_category_manager')->findOneById($documentCategoryId);
        if ($docCategory instanceof DocumentCategory) {
            $isGranted = $this->get('user_manager')->isGranted(
                'delete_document_category',
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($docCategory);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_document_category_view_all', [
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
