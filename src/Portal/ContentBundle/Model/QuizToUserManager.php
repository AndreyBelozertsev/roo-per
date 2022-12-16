<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\QuizToUser;

class QuizToUserManager
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
    private function getQuizToUserRepository()
    {
        return $this->em->getRepository('PortalContentBundle:QuizToUser');
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
     * @return \Portal\ContentBundle\Entity\QuizToUser
     */
    public function findOneById($id)
    {
        return $this->getQuizToUserRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\QuizToUser
     */
    public function find($id)
    {
        if ($id) {
            return $this->getQuizToUserRepository()->find($id);
        } else {
            return $this->getQuizToUserRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\QuizToUser[]
     */
    public function findAll()
    {
        return $this->getQuizToUserRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\QuizToUser
     */
    public function findOneBy($array)
    {
        return $this->getQuizToUserRepository()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\QuizToUser
     */
    public function findBy($array)
    {
        return $this->getQuizToUserRepository()->findBy($array);
    }

    /**
     * @param integer $userId
     *
     * @return array
     */
    public function getQuizListForUser($userId)
    {
        return $this->getQuizToUserRepository()->getQuizListForUser($userId);
    }

    /**
     * @param integer $userId
     *
     * @return array
     */
    public function getInterviewListForUser($userId)
    {
        return $this->getQuizToUserRepository()->getInterviewListForUser($userId);
    }

    /**
     * @param integer $interviewId
     *
     * @return array
     */
    public function getListUserAnswerInterview($interviewId)
    {
        return $this->getQuizToUserRepository()->getListUserAnswerInterview($interviewId);
    }

    /**
     * @param integer $quizId
     * @param integer $userId
     *
     * @return array
     */
    public function getQuizStatisticForUser($quizId, $userId)
    {
        return $this->getQuizToUserRepository()->getQuizStatisticForUser($quizId, $userId);
    }

    /**
     * @param integer $quizId
     * @param integer $userId
     *
     * @return array
     */
    public function isUserAnswer($quizId, $userId)
    {
        return $this->getQuizToUserRepository()->isUserAnswer($quizId, $userId);
    }
}
