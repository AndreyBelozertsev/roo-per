<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Portal\ContentBundle\Entity\DocumentTag;
use Portal\AdminBundle\Form\DocumentTagFormType;
use Doctrine\DBAL\DBALException;

class DocumentTagAdminController extends Controller
{
    public function indexAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), DocumentTag::PERMISSIONS_DOCUMENT_TAG);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $adapter = new ArrayAdapter($this->get('customer_document_tag_manager')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(DocumentTag::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:DocumentTagAdmin:index.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $docTag = new DocumentTag();
            $permissionCode = DocumentTag::PERMISSIONS_DOCUMENT_TAG['create'];
        } else {
            // edit
            $docTag = $this->get('customer_document_tag_manager')->findOneBy(['id' => $id]);
            if (!$docTag instanceof DocumentTag) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = DocumentTag::PERMISSIONS_DOCUMENT_TAG['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(DocumentTagFormType::class, $docTag);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($docTag);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_doc_tag_index', ['instanceCode' => $instanceCode]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:DocumentTagAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $docTag = $this->get('customer_document_tag_manager')->findOneBy(['id' => $id]);
        if ($docTag instanceof DocumentTag) {
            $isGranted = $this->get('user_manager')->isGranted(
                DocumentTag::PERMISSIONS_DOCUMENT_TAG['delete'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($docTag);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_doc_tag_index', [
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('can_not_delete');
        }

        return new JsonResponse($response);
    }
}
