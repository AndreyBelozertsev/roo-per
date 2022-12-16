<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Attachment;

class AttachmentManager
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
    private function getAttachmentRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Attachment');
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
     * @return \Portal\ContentBundle\Entity\Attachment
     */
    public function findOneById($id)
    {
        return $this->getAttachmentRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Attachment
     */
    public function find($id)
    {
        if ($id) {
            return $this->getAttachmentRepository()->find($id);
        } else {
            return $this->getAttachmentRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Attachment[]
     */
    public function findAll()
    {
        return $this->getAttachmentRepository()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Attachment
     */
    public function findOneBy($array)
    {
        return $this->getAttachmentRepository()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     * @param integer $orderBy
     * @param integer $limit
     *
     * @return \Portal\ContentBundle\Entity\Attachment
     */
    public function findBy($array, $orderBy = null, $limit = null)
    {
        return $this->getAttachmentRepository()->findBy($array, $orderBy, $limit);
    }

    /**
     * @param array $attIds
     *
     * @return \Portal\ContentBundle\Entity\Attachment
     */
    public function getAttachmentsByIds(array $attIds)
    {
        return $this->getAttachmentRepository()->getAttachmentsByIds($attIds);
    }

    /**
     *
     * @return array
     */
    public function getAllAttachments($searchParams)
    {
        return $this->getAttachmentRepository()->getAllAttachments($searchParams);
    }

    /**
     *
     * @return array
     */
    public function getDocumetnAttachments($searchParams)
    {
        return $this->getAttachmentRepository()->getDocumetnAttachments($searchParams);
    }

    /**
     *
     * @return integer
     */
    public function getCountFiles($params = [])
    {
        return $this->getAttachmentRepository()->getCountFiles($params);
    }

    /**
     *
     * @return integer
     */
    public function getCountDocumentFiles($params = [])
    {
        return $this->getAttachmentRepository()->getCountDocumentFiles($params);
    }

    /**
     *
     * @return integer
     */
    public function deleteFiles($ids = null)
    {
        return $this->getAttachmentRepository()->deleteFiles($ids);
    }

    /**
     *
     * @return integer
     */
    public function deleteFilesByIds($ids = array())
    {
        return $this->getAttachmentRepository()->deleteFilesByIds($ids);
    }
}
