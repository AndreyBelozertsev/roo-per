<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\FeedbackCategory;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\FeedbackCategoryType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class FeedbackCategoryAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), FeedbackCategory::PERMISSIONS_FEEDBACK_CATEGORY);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $feedbackCategoryList = $this->get('customer_feedback_category_manager')->getAllFeedbackCategoryForPagination();
        $adapter = new ArrayAdapter($feedbackCategoryList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:FeedbackCategoryAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $instanceCode, $id)
    {
        $feedbackCategory = $this->get('customer_feedback_category_manager')->findOneById($id);
        if (!$feedbackCategory instanceof FeedbackCategory) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            FeedbackCategory::PERMISSIONS_FEEDBACK_CATEGORY['edit'],
            $instanceCode,
            $currentUserId
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $options['validation_groups'] = ['edit'];
        $form = $this->createForm(FeedbackCategoryType::class, $feedbackCategory, $options);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($feedbackCategory);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_feedback_category_edit', [
                        'id' => $feedbackCategory->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:FeedbackCategoryAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'feedbackCategory' => $feedbackCategory,
            'instanceCode' => $instanceCode
        ]);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $feedbackCategory = new FeedbackCategory();
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            FeedbackCategory::PERMISSIONS_FEEDBACK_CATEGORY['create'],
            $instanceCode,
            $currentUserId
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $options['validation_groups'] = ['new'];
        $form = $this->createForm(FeedbackCategoryType::class, $feedbackCategory, $options);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($feedbackCategory);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_feedback_category_edit', [
                        'id' => $feedbackCategory->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:FeedbackCategoryAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'feedbackCategory' => $feedbackCategory,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $feedbackCategory = $this->get('customer_feedback_category_manager')->findOneById($id);
        if ($feedbackCategory instanceof FeedbackCategory) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(
                FeedbackCategory::PERMISSIONS_FEEDBACK_CATEGORY['delete'],
                $instanceCode,
                $currentUserId
            );
            if ($isGranted) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->remove($feedbackCategory);
                    $em->flush();

                    $response['status'] = true;
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_feedback_category_viewall', [
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {

                    return new JsonResponse([
                        'status' => false,
                        'message' => $this->get('translator')->trans('feedback_category_form.relationship_error')
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
