<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocumentAttachmentManager
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
    private function getDocumentAttachmentRepository()
    {
        return $this->em->getRepository('PortalContentBundle:DocumentAttachment');
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
     * @return \Portal\ContentBundle\Entity\DocumentAttachment
     */
    public function find($id)
    {
        return $id ? $this->getDocumentAttachmentRepository()->find($id) : $this->getDocumentAttachmentRepository()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\DocumentAttachment[]
     */
    public function findAll()
    {
        return $this->getDocumentAttachmentRepository()->findAll();
    }

    /**
     * @param integer $id
     * @return array
     */
    public function getAttachmentListByDocumentId($id)
    {
        return $this->getDocumentAttachmentRepository()->getAttachmentListByDocumentId($id);
    }
}