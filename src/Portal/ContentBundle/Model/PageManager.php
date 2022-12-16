<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PageManager
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
    private function getPageRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Page');
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
     *
     * @return array
     */
    public function getAllPages()
    {
        return $this->getPageRepository()->getAllPages();
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Page
     */
    public function find($id)
    {
        return $id ? $this->getPageRepository()->find($id) : $this->getPageRepository()->findAll();
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    public function findOneBy($array)
    {
        return $this->getPageRepository()->findOneBy($array);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getStandardTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:StandardTemplate');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getInteractiveMapTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:InteractiveMapTemplate');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getVisitcardTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:VisitcardTemplate');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getTableTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:TableTemplate');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getContactTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:ContactTemplate');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getSiteMapTemplateRepository()
    {
        return $this->em->getRepository('PortalContentBundle:SiteMapTemplate');
    }

    public function findTemplate($templateId, $pageId)
    {
        switch ($templateId) {
            case 1:
                $repository = $this->getStandardTemplateRepository();
                break;
            case 2:
                $repository = $this->getInteractiveMapTemplateRepository();
                break;
            case 3:
                $repository = $this->getVisitcardTemplateRepository();
                break;
            case 4:
                $repository = $this->getTableTemplateRepository();
                break;
            case 5:
                $repository = $this->getContactTemplateRepository();
                break;
            case 6:
                $repository = $this->getSiteMapTemplateRepository();
                break;
            default:
                $repository = false;
        }

        return $repository ? $repository->findOneBy(['pageId' => $pageId]) : false;
    }
}
