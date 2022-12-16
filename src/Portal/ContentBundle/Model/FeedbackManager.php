<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FeedbackManager
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
    private function getFeedbackRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Feedback');
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
     * @return \Portal\ContentBundle\Entity\Feedback
     */
    public function findOneById($id)
    {
        return $this->getFeedbackRepository()->findOneById($id);
    }

    /**
     * @param $id
     *
     * @return array|null|object
     */
    public function find($id)
    {
        if ($id) {
            return $this->getFeedbackRepository()->find($id);
        } else {
            return $this->getFeedbackRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Feedback[]
     */
    public function findAll()
    {
        return $this->getFeedbackRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Feedback
     */
    public function findOneBy($array)
    {
        return $this->getFeedbackRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     *
     * @return \Portal\ContentBundle\Entity\Feedback
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getFeedbackRepository()->findBy($array, $orderBy, $limit, $offset);
    }

}
