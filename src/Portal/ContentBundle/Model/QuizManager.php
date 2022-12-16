<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Quiz;

class QuizManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getQuizRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Quiz');
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Quiz
     */
    public function findOneById($id)
    {
        return $this->getQuizRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Quiz
     */
    public function find($id)
    {
        if ($id) {
            return $this->getQuizRepository()->find($id);
        } else {
            return $this->getQuizRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Quiz[]
     */
    public function findAll()
    {
        return $this->getQuizRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Quiz
     */
    public function findOneBy($array)
    {
        return $this->getQuizRepository()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Quiz
     */
    public function findBy($array)
    {
        return $this->getQuizRepository()->findBy($array);
    }

    /**
     *
     * @return array
     */
    public function getAllQuiz()
    {
        return $this->getQuizRepository()->getAllQuiz();
    }

    /**
     * @param integer $quizId
     *
     * @return array
     */
    public function getListAnswerQuiz($quizId)
    {
        return $this->getQuizRepository()->getListAnswerQuiz($quizId);
    }

    /**
     * @param integer $quizId
     *
     * @return array
     */
    public function getCorrectAnswerList($quizId)
    {
        return $this->getQuizRepository()->getCorrectAnswerList($quizId);
    }

    /**
     * @param integer $questionId
     *
     * @return array
     */
    public function getQuestionAnswerList($questionId)
    {
        return $this->getQuizRepository()->getQuestionAnswerList($questionId);
    }

    /**
     * @param integer $quizId
     * @param integer $numQuestion
     *
     * @return array
     */
    public function getQuestion($quizId, $numQuestion)
    {
        return $this->getQuizRepository()->getQuestion($quizId, $numQuestion);
    }

    /**
     * @param integer $quizId
     *
     * @return array
     */
    public function getCorrectAnswerForQuestion($questionId)
    {
        return $this->getQuizRepository()->getCorrectAnswerForQuestion($questionId);
    }
}
