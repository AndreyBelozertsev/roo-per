<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\Quiz;
use Portal\ContentBundle\Entity\QuizAnswer;
use Portal\ContentBundle\Entity\QuizQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\QuizType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class QuizAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Quiz::PERMISSIONS_QUIZ);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $adapter = new ArrayAdapter($this->get('customer_quiz_manager')->getAllQuiz());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:QuizAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(Quiz::PERMISSIONS_QUIZ['create'], $instanceCode, $currentUserId);
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $quiz = new Quiz();
        $quizQuestion = new QuizQuestion();
        $quizQuestionAnswer = new QuizAnswer();
        $quizQuestionAnswer->setIsCorrect(true);
        $quizQuestion->addAnswer($quizQuestionAnswer);
        $quizQuestion->addAnswer(new QuizAnswer());
        $quiz->addQuestion($quizQuestion);
        if ($request->query->get('menuNodeId')) {
            $quiz->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
        }
        $form = $this->createForm(QuizType::class, $quiz, [
            'listStructure' => $this->get('customer_menu_node_manager')->getMenuByCode(),
            'validation_groups' => ['new']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->getConnection()->beginTransaction();
                try {
                    $quiz->setAuthorId($currentUserId);
                    $em->persist($quiz);
                    $em->flush();
                    $em->getConnection()->commit();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                } catch (Exception $e) {
                    $em->getConnection()->rollback();
                    $flashBag->add('message', $this->get('translator')->trans('quiz_form.add_db_error'));
                }

                return $this->redirectToRoute('admin_instance_quiz_form_edit', [
                    'id' => $quiz->getId(),
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:QuizAdmin:create.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz,
            'slug' => false,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $quiz = $this->get('customer_quiz_manager')->findOneById($id);
        if (!$quiz instanceof Quiz) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(Quiz::PERMISSIONS_QUIZ['edit'], $instanceCode, $currentUserId);
        $isAuthor = ($quiz->getAuthorId() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(QuizType::class, $quiz, [
            'listStructure' => $this->get('customer_menu_node_manager')->getMenuByCode(),
            'validation_groups' => ['edit'],
            'slug' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->getConnection()->beginTransaction();
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($quiz);
                    $em->flush();
                    $em->getConnection()->commit();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                } catch (Exception $e) {
                    $em->getConnection()->rollback();
                    $flashBag->add('message', $this->get('translator')->trans('quiz_form.edit_db_error'));
                }

                return $this->redirectToRoute('admin_instance_quiz_form_edit', [
                    'id' => $quiz->getId(),
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:QuizAdmin:edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
            'slug' => true,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $quiz = $this->get('customer_quiz_manager')->findOneById($id);
        if ($quiz instanceof Quiz) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(Quiz::PERMISSIONS_QUIZ['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($quiz->getAuthorId() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($quiz);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_quiz_list', [
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
