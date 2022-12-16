<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\InterviewQuestion;

class InterviewQuestionManager
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
    private function getInterviewQuestionRepository()
    {
        return $this->em->getRepository('PortalContentBundle:InterviewQuestion');
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
     * @return \Portal\ContentBundle\Entity\InterviewQuestion
     */
    public function findOneById($id)
    {
        return $this->getInterviewQuestionRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\InterviewQuestion
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInterviewQuestionRepository()->find($id);
        } else {
            return $this->getInterviewQuestionRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\InterviewQuestion[]
     */
    public function findAll()
    {
        return $this->getInterviewQuestionRepository()->findAll();
    }

//    /**
//     * @param integer $array
//     *
//     * @return \Portal\ContentBundle\Entity\InterviewQuestion
//     */
//    public function findOneBy($array)
//    {
//        return $this->getInterviewQuestionRepository()->findOneBy($array);
//    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\InterviewQuestion
     */
    public function findOneBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        $result = $this->findBy($array, $orderBy, $limit, $offset);
        if (isset($result[0]) && $result[0] instanceof InterviewQuestion) {
            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\InterviewQuestion
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getInterviewQuestionRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     * @param int $interviewId
     * @return array
     */
    public function getInterviewQuestionList($interviewId)
    {
        return $this->getInterviewQuestionRepository()->getInterviewQuestionList($interviewId);
    }

    /**
     * @param int $interviewId
     * @return bool
     */
    public function getCountQuestion($interviewId)
    {
        return $this->getInterviewQuestionRepository()->getCountQuestion($interviewId);
    }
}
