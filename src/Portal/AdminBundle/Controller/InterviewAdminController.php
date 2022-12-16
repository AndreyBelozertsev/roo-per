<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\InterviewAnswer;
use Portal\ContentBundle\Entity\InterviewQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\ContentBundle\Entity\Interview;
use Portal\AdminBundle\Form\InterviewType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class InterviewAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Interview::PERMISSIONS_INTERVIEW);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $filterParam = $request->query->all();
        $adapter = new ArrayAdapter($this->get('customer_interview_manager')->getAllInterview($filterParam));
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:InterviewAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $filterParam,
        ]);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            Interview::PERMISSIONS_INTERVIEW['create'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $interview = new Interview();
        $interview->setMessageEnd(Interview::DEFAULT_MESSAGE_END);
        $interviewQuestion = new InterviewQuestion();
        $interviewQuestion->addAnswer(new InterviewAnswer());
        $interviewQuestion->addAnswer(new InterviewAnswer());
        $interview->addQuestion($interviewQuestion);
        if ($request->query->get('menuNodeId')) {
            $interview->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
        }
        $form = $this->createForm(InterviewType::class, $interview, [
            'listStructure' => $this->get('customer_menu_node_manager')->getMenuByCode(),
            'validation_groups' => ['new'],
            'slug' => false,
            'message_not_user' => $this->get('translator')->trans(Interview::DEFAULT_MESSAGE_NOT_REGISTERED_USER),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->getConnection()->beginTransaction();
                try {
                    $interview->setSlug(PortalHelper::slug($interview->getTitle()));
                    $interview->setAuthor($this->container->get('user_helper')->getCurrentUser()->getId());
                    $em->persist($interview);
                    $em->flush();
                    $em->getConnection()->commit();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                } catch (Exception $e) {
                    $em->getConnection()->rollback();
                    $flashBag->add('message', $this->get('translator')->trans('interview_form.add_db_error'));
                }

                return $this->redirectToRoute('admin_instance_interview_form_edit', [
                    'id' => $interview->getId(),
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:InterviewAdmin:create.html.twig', [
            'form' => $form->createView(),
            'interview' => $interview,
            'slug' => false,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $interview = $this->get('customer_interview_manager')->findOneById($id);
        if (!$interview instanceof Interview || $interview->getIsDeleted()) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            Interview::PERMISSIONS_INTERVIEW['edit'],
            $instanceCode,
            $currentUserId
        );
        $isAuthor = ($interview->getAuthor() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(InterviewType::class, $interview, [
            'listStructure' => $this->get('customer_menu_node_manager')->getMenuByCode(),
            'validation_groups' => ['edit'],
            'slug' => true,
            'message_not_user' => $interview->getMessageNotRegisteredUser(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->getConnection()->beginTransaction();
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($interview);
                    $em->flush();
                    $em->getConnection()->commit();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                } catch (Exception $e) {
                    $em->getConnection()->rollback();
                    $flashBag->add('message', $this->get('translator')->trans('interview_form.edit_db_error'));
                }

                return $this->redirectToRoute('admin_instance_interview_form_edit', [
                    'id' => $interview->getId(),
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:InterviewAdmin:edit.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
            'slug' => true,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction(Request $request, $id, $instanceCode)
    {
        $interview = $this->get('customer_interview_manager')->findOneById($id);
        if ($interview instanceof Interview) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(
                Interview::PERMISSIONS_INTERVIEW['delete'],
                $instanceCode,
                $currentUserId
            );
            $isAuthor = ($interview->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $interview->setIsDeleted(true);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($interview);
                $em->flush();

                if ($request->query->get('page') !== null) {
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_interview_list', [
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
        $interview = $this->get('customer_interview_manager')->findOneById($id);
        if ($interview instanceof Interview) {
            $isGranted = $this->get('user_manager')->isGranted(
                Interview::PERMISSIONS_INTERVIEW['restore'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $interview->setIsDeleted(false);
                $em->persist($interview);
                $em->flush();

                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_interview_list', [
                    'instanceCode' => $instanceCode
                ]);
                $response['status'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function resultAction($id, $instanceCode)
    {
        $interview = $this->get('customer_interview_manager')->find($id);
        if (!$interview instanceof Interview) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        return $this->render('PortalAdminBundle:InterviewAdmin:result.html.twig', [
            'interview' => $interview,
            'results' => $this->get('customer_interview_user_answer_manager')->getStatisticAnswer($id),
            'voted' => $this->get('customer_interview_user_answer_manager')->getVoted($id),
            'instanceCode' => $instanceCode
        ]);
    }

    public function resultVotingAction($id, $instanceCode)
    {
        $interview = $this->get('customer_interview_manager')->find($id);
        if (!$interview instanceof Interview) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        return $this->render('PortalAdminBundle:InterviewAdmin:resultVoting.html.twig', [
            'interview' => $interview,
            'results' => $this->get('customer_interview_user_answer_manager')->getInterviewAllAnswerList($id),
            'voted' => $this->get('customer_interview_user_answer_manager')->getVoted($id),
            'countQuestion' => $this->get('customer_interview_question_manager')->getCountQuestion($id),
            'instanceCode' => $instanceCode
        ]);
    }
}
