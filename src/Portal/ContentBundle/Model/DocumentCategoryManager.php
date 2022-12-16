<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\DocumentCategory;

class DocumentCategoryManager
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
    private function getDocumentCategoryRepository()
    {
        return $this->em->getRepository('PortalContentBundle:DocumentCategory');
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
     * @return array
     */
    public function getAllDocumentCategory()
    {
        return $this->getDocumentCategoryRepository()->getAllDocumentCategory();
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\DocumentCategory
     */
    public function findOneById($id)
    {
        return $this->getDocumentCategoryRepository()->findOneById($id);
    }

    /**
     * @return \Portal\ContentBundle\Entity\DocumentCategory[]
     */
    public function findAll()
    {
        return $this->getDocumentCategoryRepository()->findAll();
    }
}
