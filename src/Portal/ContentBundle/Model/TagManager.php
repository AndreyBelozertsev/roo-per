<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagManager
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
    private function getTagRepo()
    {
        return $this->em->getRepository('PortalContentBundle:Tag');
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
     * @return \Portal\ContentBundle\Entity\Tag
     */
    public function findOneById($id)
    {
        return $this->getTagRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Tag
     */
    public function find($id)
    {
        return $id ? $this->getTagRepo()->find($id) : $this->getTagRepo()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\Tag[]
     */
    public function findAll()
    {
        return $this->getTagRepo()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Tag
     */
    public function findOneBy($array)
    {
        return $this->getTagRepo()->findOneBy($array);
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Tag
     */
    public function findBy($array, $orderBy = null)
    {
        return $this->getTagRepo()->findBy($array, $orderBy);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getTagsByArticleId($id)
    {
        return $this->getTagRepo()->getTagsByArticleId($id);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getTagsByDocumentId($id)
    {
        return $this->getTagRepo()->getTagsByDocumentId($id);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getTagsByEventId($id)
    {
        return $this->getTagRepo()->getTagsByEventId($id);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getTagsByPhotoReportId($id)
    {
        return $this->getTagRepo()->getTagsByPhotoReportId($id);
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getTagsByVideoReportId($id)
    {
        return $this->getTagRepo()->getTagsByVideoReportId($id);
    }

    /**
     *
     * @return array
     */
    public function getAllDocumentsTags()
    {
        return $this->getTagRepo()->getAllDocumentsTags();
    }
}
