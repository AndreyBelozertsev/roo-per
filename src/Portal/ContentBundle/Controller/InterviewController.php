<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\Interview;
use Portal\ContentBundle\Entity\InterviewUserAnswer;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\DBALException;

class InterviewController extends Controller
{
    public function showAction(Request $request)
    {
        $arrParam['user'] = true;
        $user = $this->getUser();
        if ($user) {
            $arrParam['userId'] = $user->getId();
        }
        if ($request->get('slug')) {
            $slug = $request->get('slug');
            $interview = $this->get('customer_interview_manager')->findOneBy(['slug' => $slug]);
        } else {
            $id = $request->get('id');
            $interview = $this->get('customer_interview_manager')->findOneBy(['id' => $id]);
        }
        if (!$interview instanceof Interview || $interview->getIsDeleted()) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        $id = $interview->getId();
        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        $arrParam['interview'] = $interview;
        if ($interview->getIsRegisteredUser()) {
            if (!$user) {
                $arrParam['user'] = false;

                return $this->render('PortalContentBundle:Interview:show.html.twig', $arrParam);
            }
        }
        $arrParam['results'] = $this->get('customer_interview_user_answer_manager')->getStatisticAnswer($id);
        $arrParam['voted'] = $this->get('customer_interview_user_answer_manager')->getVoted($id);

        if ($user) {
            if ($this->get('customer_interview_user_answer_manager')->isUserAnswer($id, $user->getId())) {
                return $this->render('PortalContentBundle:Interview:showResult.html.twig', $arrParam);
            }
        }
        $date = new \DateTime();
        if ($date->format('Y-m-d') > $interview->getDateEnd()->format('Y-m-d')) {

            return $this->render('PortalContentBundle:Interview:showResult.html.twig', $arrParam);
        }
        $interview_finish_ids = explode(',', $request->cookies->get('interview_finish_ids'));
        if (in_array($id, $interview_finish_ids)) {

            return $this->render('PortalContentBundle:Interview:showResult.html.twig', $arrParam);
        }

        $arrParam['question'] = $this->get('customer_interview_question_manager')->findOneBy(
            ['interviewId' => $id],
            ['id' => 'ASC'],
            1
        );
        $answerToken = $this->generateAnswerToken();
        $session = $request->getSession();
        $session->set('answer_token', $answerToken);
        $arrParam['uniqueId'] = PortalHelper::uuidV4Generate();
        $arrParam['answerToken'] = $answerToken;
        $arrParam['numQuestion'] = 1;
        $arrParam['interviewId'] = $id;
        $arrParam['countQuestions'] = $interview->getQuestions()->count();

        return $this->render('PortalContentBundle:Interview:show.html.twig', $arrParam);
    }

    public function resultAction(Request $request, $id)
    {
        $interview = $this->get('customer_interview_manager')->find($id);
        if (!$interview instanceof Interview) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        $arrParam['user'] = true;
        if ($interview->getIsRegisteredUser()) {
            if (!$this->getUser()) {
                $arrParam['user'] = false;
            }
        }

        $arrParam['isFinish'] = ($request->query->get('isFinish') === 'true');
        $interview_finish_ids = explode(',', $request->cookies->get('interview_finish_ids'));
        if ($arrParam['isFinish']) {
            if (!in_array($id, $interview_finish_ids)) {
                $interview_finish_ids[] = $id;
            }
        }

        $arrParam['interview'] = $interview;
        $arrParam['results'] = $this->get('customer_interview_user_answer_manager')->getStatisticAnswer($id);
        $arrParam['voted'] = $this->get('customer_interview_user_answer_manager')->getVoted($id);

        $response = $this->render('PortalContentBundle:Interview:showResult.html.twig', $arrParam);
        $response->headers->setCookie(
            new Cookie('interview_finish_ids', implode(',', $interview_finish_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    public function getNextQuestionAction(Request $request)
    {
        $response = [
            'message' => '',
            'status' => false,
            'content' => '',
            'isFinish' => false,
        ];
        try {
            $session = $request->getSession();
            $sessionAnswerToken = $session->get('answer_token');
            $requestAnswerToken = $request->request->get('answer_token');
            $newAnswerToken = "";
            $interviewId = $request->request->get('interviewId');
            $userId = $request->request->get('userId');
            $questionId = $request->request->get('questionId');
            $numQuestion = $request->request->get('numQuestion');
            $answers = $request->request->get('answer_id');
            $content = $request->request->get('content');
            $answerSelect = $request->request->get('answer_select');
            $uniqueId = $request->request->get('uniqueId');
            $em = $this->getDoctrine()->getManager('customer');
            if ($sessionAnswerToken === $requestAnswerToken) {
                if ($answers || $answerSelect || $content) {
                    if ($answers) {
                        $question = $this->get('customer_interview_question_manager')->find($questionId);
                        foreach ($answers as $answer) {
                            $interviewUserAnswer = new InterviewUserAnswer();
                            $interviewUserAnswer->setQuestionId($question);
                            $interviewUserAnswer->setAnswerId($this->get('customer_interview_answer_manager')->find($answer));
                            $interviewUserAnswer->setUniqueId($uniqueId);
                            if ($userId) {
                                $interviewUserAnswer->setUserId($userId);
                            }
                            $em->persist($interviewUserAnswer);
                        }
                    }
                    if ($answerSelect) {
                        $interviewUserAnswer = new InterviewUserAnswer();
                        $interviewUserAnswer->setQuestionId($this->get('customer_interview_question_manager')->find($questionId));
                        $interviewUserAnswer->setAnswerId($this->get('customer_interview_answer_manager')->find($answerSelect));
                        $interviewUserAnswer->setUniqueId($uniqueId);
                        if ($userId) {
                            $interviewUserAnswer->setUserId($userId);
                        }
                        $em->persist($interviewUserAnswer);
                    }
                    if ($content) {
                        $interviewUserAnswer = new InterviewUserAnswer();
                        $interviewUserAnswer->setQuestionId($this->get('customer_interview_question_manager')->find($questionId));
                        $interviewUserAnswer->setContent($content);
                        $interviewUserAnswer->setUniqueId($uniqueId);
                        if ($userId) {
                            $interviewUserAnswer->setUserId($userId);
                        }
                        $em->persist($interviewUserAnswer);
                    }
                } else {
                    $interviewUserAnswer = new InterviewUserAnswer();
                    $interviewUserAnswer->setQuestionId($this->get('customer_interview_question_manager')->find($questionId));
                    $interviewUserAnswer->setUniqueId($uniqueId);
                    $interviewUserAnswer->setIsSkip(true);
                    if ($userId) {
                        $interviewUserAnswer->setUserId($userId);
                    }
                    $em->persist($interviewUserAnswer);
                }
                $em->flush();
                $newAnswerToken = $this->generateAnswerToken();
                $session->set('answer_token', $newAnswerToken);
            }
            while (true) {
                if (
                    !$question = $this->get('customer_interview_question_manager')->findOneBy(
                    ['interviewId' => $interviewId],
                    ['id' => 'ASC'],
                    1,
                    $numQuestion)
                ) {
                    break;
                }
                if ($question->getDependentInterviewAnswerId() !== null) {
                    if (!$this->get('customer_interview_user_answer_manager')->isVotedDependentAnswer($question->getDependentInterviewAnswerId(),$uniqueId)) {
                        $numQuestion++;
                    } else {
                        break;
                    }
                } else {
                    break;
                }

            }
            $formParams['interviewId'] = $interviewId;
            $formParams['answerToken'] = $newAnswerToken;
            $formParams['numQuestion'] = $numQuestion + 1;
            $formParams['question'] = $question;
            if ($question) {
                $response['content'] = $this->render('PortalContentBundle:Interview:includeForm.html.twig', $formParams)->getContent();
                $response['status'] = true;
            } else {
                if ($this->get('customer_interview_user_answer_manager')->updateIsDraft($uniqueId)) {
                    $response['id'] = $interviewId;
                    $response['isFinish'] = true;
                } else {
                    $response['content'] = 'не';
                }
            }
        } catch (DBALException $e) {
            $response['message'] = $this->get('translator')->trans('query_error');
        }

        return new JsonResponse($response);
    }

    /**
     * Redirect to page authorize esia.
     *
     * @param Request $request
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function interviewEsiaLoginAction (Request $request, $slug)
    {
        $session = $request->getSession();
        $options['route'] = 'interview_show_slug';
        $options['param'] = 'slug';
        $options['value'] = $slug;
        $session->set('for_esia_redirect_to', $options);

        return $this->redirectToRoute('esia_login');
    }

    /**
     * generate answer token.
     *
     * @return string
     */
    public function generateAnswerToken()
    {
        return md5(time() . rand(111111, 911111));
    }
}
