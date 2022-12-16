<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Interview;

class InterviewManager
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
    private function getInterviewRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Interview');
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
     * @return \Portal\ContentBundle\Entity\Interview
     */
    public function findOneById($id)
    {
        return $this->getInterviewRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Interview
     */
    public function find($id)
    {
        if ($id) {
            return $this->getInterviewRepository()->find($id);
        } else {
            return $this->getInterviewRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Interview[]
     */
    public function findAll()
    {
        return $this->getInterviewRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Interview
     */
    public function findOneBy($array)
    {
        return $this->getInterviewRepository()->findOneBy($array);
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Interview
     */
    public function findBy($array)
    {
        return $this->getInterviewRepository()->findBy($array);
    }

    /**
     *
     * @return array
     */
    public function getAllInterview($filterParam)
    {
        return $this->getInterviewRepository()->getAllInterview($filterParam);
    }
}
