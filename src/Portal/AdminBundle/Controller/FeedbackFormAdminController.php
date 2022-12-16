<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\FeedbackForm;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\FeedbackFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class FeedbackFormAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), FeedbackForm::PERMISSIONS_FEEDBACK_FORM);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $feedbackFormList = $this->get('customer_feedback_form_manager')->getAllFeedbackFormForPagination();
        $adapter = new ArrayAdapter($feedbackFormList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:FeedbackFormAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $instanceCode, $id)
    {
        $feedbackForm = $this->get('customer_feedback_form_manager')->findOneById($id);
        if (!$feedbackForm instanceof FeedbackForm) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        if ($request->query->get('menuNodeId')) {
            $feedbackForm->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(FeedbackForm::PERMISSIONS_FEEDBACK_FORM['edit'], $instanceCode, $currentUserId);
        $isAuthor = ($feedbackForm->getAuthorId() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $data = json_decode($feedbackForm->getSortOptions());
        $options['message_success'] = $feedbackForm->getMessageSuccess();
        $options['message_error'] = $feedbackForm->getMessageError();
        $options['visible_data'] = $data;
        $options['validation_groups'] = ['edit'];
        $options['slug'] = true;
        $form = $this->createForm(FeedbackFormType::class, $feedbackForm, $options);
        $form->handleRequest($request);
        $formParams['form'] = $form->createView();
        $formParams['feedbackForm'] = $feedbackForm;
        $formParams['sortFieldList'] = $data;
        $formParams['esiaFieldsList'] = json_decode($feedbackForm->getEsiaFields());
        $formParams['slug'] = true;
        if ($request->isMethod('POST')) {
            if ($request->request->get('sort') != null) {
                $sort = explode(',', $request->request->get('sort'));
            } else {
                $sort = null;
            }
            $esiaFields = [];
            if ($request->request->get('esiaFields') != null) {
                $esiaFields = explode(',', $request->request->get('esiaFields'));
            }
            $formParams['sortFieldList'] = $sort;
            $formParams['esiaFieldsList'] = $esiaFields;
            $flashBag = $this->get('session')->getFlashBag();

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $feedbackForm->setSortOptions(json_encode($sort));
                    $feedbackForm->setEsiaFields(json_encode($esiaFields));
                    $feedbackForm->setAuthorId($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($feedbackForm);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_admin_feedback_form_edit', [
                        'id' => $feedbackForm->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }
        $formParams['instanceCode'] = $instanceCode;

        return $this->render('PortalAdminBundle:FeedbackFormAdmin:edit.html.twig', $formParams);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $feedbackForm = new FeedbackForm();
        if ($request->query->get('menuNodeId')) {
            $feedbackForm->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(FeedbackForm::PERMISSIONS_FEEDBACK_FORM['create'], $instanceCode, $currentUserId);
        $isAuthor = ($feedbackForm->getAuthorId() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $options['message_success'] = $this->get('translator')->trans(FeedbackForm::DEFAULT_MESSAGE_SUCCESS);
        $options['message_error'] = $this->get('translator')->trans(FeedbackForm::DEFAULT_MESSAGE_ERROR);
        $options['visible_data'] = FeedbackForm::FIELD_SELECTED_DEFAULT_LIST;
        $formParams['esiaFieldsList'] = FeedbackForm::FIELD_ESIA_LIST;
        $options['validation_groups'] = ['new'];
        $options['slug'] = false;
        $form = $this->createForm(FeedbackFormType::class, $feedbackForm, $options);
        $form->handleRequest($request);
        $formParams['form'] = $form->createView();
        $formParams['feedbackForm'] = $feedbackForm;
        $formParams['sortFieldList'] = FeedbackForm::FIELD_SELECTED_DEFAULT_LIST;
        $formParams['slug'] = false;
        if ($request->isMethod('POST')) {
            $sort = [];
            if ($request->request->get('sort') != null) {
                $sort = explode(',', $request->request->get('sort'));
            }
            $esiaFields = [];
            if ($request->request->get('esiaFields') != null) {
                $esiaFields = explode(',', $request->request->get('esiaFields'));
            }
            $formParams['sortFieldList'] = $sort;
            $formParams['esiaFieldsList'] = $esiaFields;
            $flashBag = $this->get('session')->getFlashBag();

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $feedbackForm->setSortOptions(json_encode($sort));
                    $feedbackForm->setEsiaFields(json_encode($esiaFields));
                    $feedbackForm->setAuthorId($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($feedbackForm);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_admin_feedback_form_edit', [
                        'id' => $feedbackForm->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }
        $formParams['instanceCode'] = $instanceCode;

        return $this->render('PortalAdminBundle:FeedbackFormAdmin:edit.html.twig', $formParams);
    }

    public function deleteAction($id, $instanceCode)
    {
        $feedbackForm = $this->get('customer_feedback_form_manager')->findOneById($id);
        if ($feedbackForm instanceof FeedbackForm) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(FeedbackForm::PERMISSIONS_FEEDBACK_FORM['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($feedbackForm->getAuthorId() === $currentUserId);
            if ($isGranted || $isAuthor) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->remove($feedbackForm);
                    $em->flush();

                    $response['status'] = true;
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_feedback_viewall', [
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {

                    return new JsonResponse([
                        'status' => false,
                        'message' => $this->get('translator')->trans('feedback_form.relationship_error')
                    ]);
                }
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
