<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\WidgetToPanel;

class WidgetToPanelManager
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
    private function getWidgetToPanelRepository()
    {
        return $this->em->getRepository('PortalContentBundle:WidgetToPanel');
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
     * @return \Portal\ContentBundle\Entity\WidgetToPanel
     */
    public function findOneById($id)
    {
        return $this->getWidgetToPanelRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\WidgetToPanel
     */
    public function find($id)
    {
        if ($id) {
            return $this->getWidgetToPanelRepository()->find($id);
        } else {
            return $this->getWidgetToPanelRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\WidgetToPanel[]
     */
    public function findAll()
    {
        return $this->getWidgetToPanelRepository()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\ContentBundle\Entity\WidgetToPanel
     */
    public function findOneBy($array)
    {
        return $this->getWidgetToPanelRepository()->findOneBy($array);
    }
    
    /**
     * @param integer $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @return \Portal\ContentBundle\Entity\WidgetToPanel
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getWidgetToPanelRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     *
     * @return array
     */
    public function getAllWidgetToPanelForPagination()
    {
        return $this->getWidgetToPanelRepository()->getAllWidgetToPanelForPagination();
    }
    
    /**
     *
     * @return array
     */
    public function setWidgetToPanelOrder($sortOrder)
    {
        return $this->getWidgetToPanelRepository()->setWidgetToPanelOrder($sortOrder);
    }

    /**
     *
     * @param string $panel
     * @param string $codePageTemplate
     * @return array
     */
    public function getListWidgetForPanel($panel, $codePageTemplate)
    {
        return $this->getWidgetToPanelRepository()->getListWidgetForPanel($panel, $codePageTemplate);
    }

    /**
     *
     * @param integer $id
     * @return array
     */
    public function getCodeWidget($id)
    {
        return $this->getWidgetToPanelRepository()->getCodeWidget($id);
    }
}
