<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\InterviewAnswer;

class InterviewAnswerManager
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
    private function getInterviewAnswerRepository()
    {
        return $this->em->getRepository('PortalContentBundle:InterviewAnswer');
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
     * @return \Portal\ContentBundle\Entity\InterviewAnswer
     */
    public function findOneById($id)
    {
        return $this->getInterviewAnswerRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\InterviewAnswer
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInterviewAnswerRepository()->find($id);
        } else {
            return $this->getInterviewAnswerRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\InterviewAnswer[]
     */
    public function findAll()
    {
        return $this->getInterviewAnswerRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\InterviewAnswer
     */
    public function findOneBy($array)
    {
        return $this->getInterviewAnswerRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\InterviewAnswer
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getInterviewAnswerRepository()->findBy($array, $orderBy, $limit, $offset);
    }

}
