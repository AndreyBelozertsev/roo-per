<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\FeedbackCategory;

class FeedbackCategoryManager
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
    private function getFeedbackCategoryRepository()
    {
       return $this->em->getRepository('PortalContentBundle:FeedbackCategory');
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
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function findOneById($id)
    {
       return $this->getFeedbackCategoryRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function find($id)
    {
       if ($id) {
           return $this->getFeedbackCategoryRepository()->find($id);
        } else {
           return $this->getFeedbackCategoryRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\FeedbackCategory[]
     */
    public function findAll()
    {
       return $this->getFeedbackCategoryRepository()->findAll();
    }

    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function findOneBy($array)
    {
       return $this->getFeedbackCategoryRepository()->findOneBy($array);
    }

    /**
     * @param integer $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return \Portal\ContentBundle\Entity\FeedbackCategory
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
       return $this->getFeedbackCategoryRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     *
     * @return array
     */
    public function getAllFeedbackCategoryForPagination()
    {
       return $this->getFeedbackCategoryRepository()->getAllFeedbackCategoryForPagination();
    }
}