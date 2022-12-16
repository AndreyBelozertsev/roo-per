<?php

namespace Portal\ContentBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\ContentBundle\Entity\Quiz;
use Portal\ContentBundle\Entity\QuizToUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuizController extends Controller
{
    public function showAction(Request $request)
    {
        if ($this->getUser()) {
            $arrParam['userId'] = $this->getUser()->getId();
        }
        if ($request->get('slug')) {
            $slug = $request->get('slug');
            $quiz = $this->get('customer_quiz_manager')->findOneBy(['slug' => $slug]);
        } else {
            $id = $request->get('id');
            $quiz = $this->get('customer_quiz_manager')->find($id);
        }
        if (!$quiz instanceof Quiz) {

            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }
        if (!$quiz->getIsPublished()) {

            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }
        $id = $quiz->getId();
        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $arrParam['quiz'] = $quiz;
        $date = new \DateTime();
        $arrParam['dateStart'] = true;
        if ($date->format('Y-m-d') > $quiz->getDateEnd()->format('Y-m-d')) {
            $arrParam['dateEnd'] = true;

            return $this->redirectToRoute('quiz_result', ['id' => $id]);
        }
        if ($date->format('Y-m-d') < $quiz->getDateStart()->format('Y-m-d')) {
            $arrParam['dateStart'] = false;
        }
        $quiz_finish_ids = explode(',', $request->cookies->get('quiz_finish_ids'));
        if (in_array($id, $quiz_finish_ids)) {

            return $this->redirectToRoute('quiz_result', ['id' => $id]);
        }
        if ($this->getUser()) {
            if ($this->get('customer_quiz_to_user_manager')->isUserAnswer($id, $arrParam['userId'])) {
                return $this->redirectToRoute('quiz_result', ['id' => $id]);
            }
        }
        $numQuestion = 1;
        $arrParam['numQuestion'] = $numQuestion;
        $arrParam['countQuestions'] = $quiz->getQuestions()->count();
        $question = $this->get('customer_quiz_manager')->getQuestion($id, $numQuestion);
        $arrParam['question'] = $question;
        $arrParam['quizId'] = $id;
        $arrParam['countCorrectAnswer'] = 0;
        $arrParam['answerList'] = $this->get('customer_quiz_manager')->getQuestionAnswerList($question['question_id']);

        return $this->render('PortalContentBundle:Quiz:show.html.twig', $arrParam);
    }

    public function resultAction(Request $request, $id)
    {
        $arrParam['quizFinish'] = ($request->query->get('isFinish') === 'true');
        $arrParam['goodResult'] = ($request->query->get('goodResult') === 'true');
        $arrParam['countCorrectAnswer'] = $request->query->get('countCorrectAnswer');
        $quiz = $this->get('customer_quiz_manager')->find($id);
        if (!$quiz instanceof Quiz) {

            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }
        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $quiz_finish_ids = explode(',', $request->cookies->get('quiz_finish_ids'));
        if ($arrParam['quizFinish']) {
            if (!in_array($id, $quiz_finish_ids)) {
                $quiz_finish_ids[] = $id;
            }
        }
        $arrParam['quiz'] = $quiz;
        $arrParam['countQuizAnswer'] = $quiz->getQuestions()->count();
        $response = $this->render('PortalContentBundle:Quiz:showResult.html.twig', $arrParam);
        $response->headers->setCookie(
            new Cookie('quiz_finish_ids', implode(',', $quiz_finish_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    public function getNextQuestionAction(Request $request)
    {
        $quizId = $request->request->get('quizId');
        $userId = $request->request->get('userId');
        $numQuestion = (int)$request->request->get('numQuestion');
        $questionId = (int)$request->request->get('questionId');
        $countCorrectAnswer = (int)$request->request->get('countCorrectAnswer');
        $answers = $request->request->get('answers');
        $quizAnswers = $this->get('customer_quiz_manager')->getCorrectAnswerForQuestion($questionId);
        if ($answers == $quizAnswers) {
            $countCorrectAnswer++;
        }
        $numQuestion++;
        $arrParam['numQuestion'] = $numQuestion;
        $arrParam['countCorrectAnswer'] = $countCorrectAnswer;
        $responseParam = [
            'message' => '',
            'status' => false,
            'content' => '',
            'quizFinish' => false,
        ];
        try {
            $question = $this->get('customer_quiz_manager')->getQuestion($quizId, $numQuestion);

            if ($question) {
                $arrParam['answerList'] = $this->get('customer_quiz_manager')->getQuestionAnswerList($question['question_id']);
                $arrParam['question'] = $question;
                $responseParam['content'] = $this->render('PortalContentBundle:Quiz:includeForm.html.twig', $arrParam)->getContent();
                $responseParam['status'] = true;
            } else {
                $quiz = $this->get('customer_quiz_manager')->find($quizId);
                $countQuizAnswer = $quiz->getQuestions()->count();
                $correctPercentAnswer = $countCorrectAnswer * 100 / $countQuizAnswer;
                $responseParam['goodResult'] = false;
                if ($correctPercentAnswer >= $quiz->getCriterion()) {
                    $quiz->setGoodRespondentsCounter((int)$quiz->getGoodRespondentsCounter() + 1);
                    $responseParam['goodResult'] = true;
                }
                $quiz->setRespondentsCounter((int)$quiz->getRespondentsCounter() + 1);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($quiz);
                if ($userId) {
                    $quizUser = new QuizToUser();
                    $quizUser->setUserId((int)$userId);
                    $quizUser->setQuiz($quiz);
                    $quizUser->setCorrectAnswer($countCorrectAnswer);
                    $quizUser->setCriterion((int)$correctPercentAnswer);
                    $quizUser->setIsGoodResult($responseParam['goodResult']);
                    $em->persist($quizUser);
                }
                $em->flush();
                $responseParam['countCorrectAnswer'] = $arrParam['countCorrectAnswer'];
                $responseParam['quizFinish'] = true;
                $responseParam['quizId'] = $quizId;
            }
        } catch (DBALException $e) {
            $responseParam['message'] = $this->get('translator')->trans('query_error');
        }

        return new JsonResponse($responseParam);
    }
}
