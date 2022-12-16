<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\InterviewUserAnswer;

class InterviewUserAnswerManager
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
    private function getInterviewUserAnswerRepository()
    {
        return $this->em->getRepository('PortalContentBundle:InterviewUserAnswer');
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
     * @return \Portal\ContentBundle\Entity\InterviewUserAnswer
     */
    public function findOneById($id)
    {
        return $this->getInterviewUserAnswerRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\InterviewUserAnswer
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInterviewUserAnswerRepository()->find($id);
        } else {
            return $this->getInterviewUserAnswerRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\InterviewUserAnswer[]
     */
    public function findAll()
    {
        return $this->getInterviewUserAnswerRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\InterviewUserAnswer
     */
    public function findOneBy($array)
    {
        return $this->getInterviewUserAnswerRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\InterviewUserAnswer
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getInterviewUserAnswerRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    public function updateIsDraft($uniqueId)
    {
        return $this->getInterviewUserAnswerRepository()->updateIsDraft($uniqueId);
    }

    public function getStatisticAnswer($interviewId)
    {
        return $this->getInterviewUserAnswerRepository()->getStatisticAnswer($interviewId);
    }

    public function isUserAnswer($interviewId, $userId)
    {
        return $this->getInterviewUserAnswerRepository()->isUserAnswer($interviewId, $userId);
    }

    public function getVoted($interviewId)
    {
        return $this->getInterviewUserAnswerRepository()->getVoted($interviewId);
    }

    /**
     * @param int $interviewId
     * @return array
     */
    public function getInterviewAllAnswerList($interviewId)
    {
        return $this->getInterviewUserAnswerRepository()->getInterviewAllAnswerList($interviewId);
    }

    /**
     * @param int $interviewId
     * @param int|null $lastUserAnswerId
     * @return array
     */
    public function getInterviewVotedList($interviewId, $lastUserAnswerId = null)
    {
        return $this->getInterviewUserAnswerRepository()->getInterviewVotedList($interviewId, $lastUserAnswerId);
    }

    /**
     * @param string $uniqueId
     * @return array
     */
    public function getVotedAnswerList($uniqueId)
    {
        return $this->getInterviewUserAnswerRepository()->getVotedAnswerList($uniqueId);
    }

    /**
     * @param int $interviewId
     * @return boolean
     */
    public function updateDraftUserAnswer($interviewId)
    {
        return $this->getInterviewUserAnswerRepository()->updateDraftUserAnswer($interviewId);
    }

    /**
     * @param int $idAnswer
     * @param string $uniqueId
     * @return bool
     */
    public function isVotedDependentAnswer($idAnswer, $uniqueId)
    {
        return $this->getInterviewUserAnswerRepository()->isVotedDependentAnswer($idAnswer, $uniqueId);
    }
}
