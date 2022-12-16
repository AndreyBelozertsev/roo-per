<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\StructureTemplate;

class StructureTemplateManager
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
    private function getStructureTemplateRepo()
    {
        return $this->em->getRepository('PortalContentBundle:StructureTemplate');
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
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findOneById($id)
    {
        return $this->getStructureTemplateRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function find($id)
    {
        if ($id) {
            return $this->getStructureTemplateRepo()->find($id);
        } else {
            return $this->getStructureTemplateRepo()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Menu[]
     */
    public function findAll()
    {
        return $this->getStructureTemplateRepo()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findOneBy($array)
    {
        return $this->getStructureTemplateRepo()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\Menu
     */
    public function findBy($array)
    {
        return $this->getStructureTemplateRepo()->findBy($array);
    }

    /**
     *@param integer $structureId
     *@param integer $offset
     *
     * @return array
     */
    public function getStructureInterviewsList($structureId, int $offset = 0)
    {
        return $this->getStructureTemplateRepo()->getStructureInterviewsList($structureId, $offset);
    }

    /**
     *@param integer $structureId
     *@param integer $offset
     *
     * @return array
     */
    public function getStructureArticlesList($structureId, int $offset = 0)
    {
        return $this->getStructureTemplateRepo()->getStructureArticlesList($structureId, $offset);
    }

    /**
     *@param integer $structureId
     *
     * @return itneger|false
     */
    public function getMaxOrderChildStructure($structureId)
    {
        return $this->getStructureTemplateRepo()->getMaxOrderChildStructure($structureId);
    }
}
