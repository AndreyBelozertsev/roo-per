<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\FeedbackForm;

class FeedbackFormManager
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
    private function getFeedbackFormRepository()
    {
       return $this->em->getRepository('PortalContentBundle:FeedbackForm');
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
     * @return \Portal\ContentBundle\Entity\FeedbackForm
     */
    public function findOneById($id)
    {
       return $this->getFeedbackFormRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\FeedbackForm
     */
    public function find($id)
    {
       if ($id) {
           return $this->getFeedbackFormRepository()->find($id);
        } else {
           return $this->getFeedbackFormRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\FeedbackForm[]
     */
    public function findAll()
    {
       return $this->getFeedbackFormRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\FeedbackForm
     */
    public function findOneBy($array)
    {
       return $this->getFeedbackFormRepository()->findOneBy($array);
    }

    /**
     * @param integer $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\FeedbackForm
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
       return $this->getFeedbackFormRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     *
     * @return array
     */
    public function getAllFeedbackFormForPagination()
    {
       return $this->getFeedbackFormRepository()->getAllFeedbackFormForPagination();
    }

    /**
     *
     * @return array
     */
    public function getFeedbackForm()
    {
       return $this->getFeedbackFormRepository()->getFeedbackForm();
    }
}