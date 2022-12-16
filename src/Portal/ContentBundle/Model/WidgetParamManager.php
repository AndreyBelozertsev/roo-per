<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WidgetParamManager
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
    private function getWidgetParamRepository()
    {
        return $this->em->getRepository('PortalContentBundle:WidgetParam');
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
     * @return \Portal\ContentBundle\Entity\WidgetParam
     */
    public function find($id)
    {
        return $id ? $this->getWidgetParamRepository()->find($id) : $this->getWidgetParamRepository()->findAll();
    }

    /**
     * @param array $paramNames
     * @param integer $id
     * @return mixed
     */
    public function getParamsByNamesAndId(array $paramNames, $id)
    {
        return $this->getWidgetParamRepository()->getParamsByNamesAndId($paramNames, $id);
    }
    
    /**
     * @param array $paramNames
     * @param integer $id
     * @return mixed
     */
    public function getNotEmptyParamsByNamesAndId(array $paramNames, $id)
    {
        return $this->getWidgetParamRepository()->getNotEmptyParamsByNamesAndId($paramNames, $id);
    }

    /**
     * @param string $paramName
     * @return mixed
     */
    public function getParamByName(string $paramName)
    {
        return $this->getWidgetParamRepository()->getParamByName($paramName);
    }
    
    /**
     * @param string $paramName
     * @param int $id
     * @return mixed
     */
    public function getParamByNameAndId(string $paramName, $id)
    {
        return $this->getWidgetParamRepository()->getParamByNameAndId($paramName, $id);
    }
}