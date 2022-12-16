<?php

namespace Portal\AdminBundle\Controller;

use Portal\HelperBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\DocumentFormType;
use Portal\ContentBundle\Entity\Document;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Doctrine\DBAL\DBALException;

class DocumentAdminController extends Controller
{
    /**
     * @param Request $request
     * @param $instanceCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAllAction(Request $request, $instanceCode)
    {
        $filterParam = $request->query->all();
        $adapter = new ArrayAdapter($this->get('customer_document_manager')->getAllDocumentForPagination($filterParam));
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(Document::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:DocumentAdmin:viewAll.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $filterParam
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $instanceCode
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $document = new Document();
            if ($request->query->get('menuNodeId')) {
                $document->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
            }
            $permissionCode = Document::PERMISSIONS_DOCUMENT['create'];
            $isAuthor = false;
            $slug = false;
            $validation_group = ['new'];
            $document->setIsSearchDocumentIndexed(FALSE);
            $document->setIsSearchIndexed(FALSE);
        } else {
            // edit
            $document = $this->get('customer_document_manager')->findOneById($id);
            if (!$document instanceof Document || $document->getIsDeleted()) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = Document::PERMISSIONS_DOCUMENT['edit'];
            $isAuthor = ($document->getAuthor() === $currentUserId);
            $slug = true;
            $validation_group = ['edit'];
            $document->setIsSearchDocumentIndexed(FALSE);
            $document->setIsSearchIndexed(FALSE);
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(DocumentFormType::class, $document, [
            'permissions' => array_column($this->get('permissions')->getAll(), 'code'),
            'isSuperAdmin' => $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'),
            'slug' => $slug,
            'validation_groups' => $validation_group,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $document->setAuthor($this->get('user_helper')->getCurrentUser()->getId());
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($document);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_document_edit', [
                        'id' => $document->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:DocumentAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode,
            'slug' => $slug,
            'document' => $document,
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $instanceCode
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id, $instanceCode)
    {
        $document = $this->get('customer_document_manager')->findOneById($id);
        if ($document instanceof Document) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(
                Document::PERMISSIONS_DOCUMENT['delete'],
                $instanceCode,
                $currentUserId
            );
            $isAuthor = ($document->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $document->setIsDeleted(true);
                $document->setIsSearchDocumentIndexed(FALSE);
                $document->setIsSearchIndexed(FALSE);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($document);
                $em->flush();

                if ($request->query->get('page') !== null) {
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_document_view_all', [
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

    /**
     * @param $id
     * @param $instanceCode
     * @return JsonResponse
     */
    public function restoreAction($id, $instanceCode)
    {
        $document = $this->get('customer_document_manager')->findOneById($id);
        if ($document instanceof Document) {
            $isGranted = $this->get('user_manager')->isGranted(
                Document::PERMISSIONS_DOCUMENT['restore'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $document->setIsDeleted(false);
                $document->setIsSearchDocumentIndexed(FALSE);
                $document->setIsSearchIndexed(FALSE);
                $em->persist($document);
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

    /**
     * @param $id
     * @param $instanceCode
     * @return JsonResponse
     */
    public function checkedOnIdAction($id, $instanceCode)
    {
        $response['status'] = false;
        $document = $this->get('customer_document_manager')->findOneBy(['id' => $id]);
        if ($document instanceof Document) {
            $isGranted = $this->get('user_manager')->isGranted(
                Document::PERMISSIONS_DOCUMENT['edit'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $isLinked = $document->getIsLinkOnId() ? false : true;
                $document->setIsLinkOnId($isLinked);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($document);
                $em->flush();
                $response['status'] = true;
                $response['message'] = $this->get('translator')->trans('successfully_save');
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('wrong_data');
        }

        return new JsonResponse($response);
    }
}
