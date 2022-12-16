<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\Slider;

class SliderManager
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
    private function getSliderRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Slider');
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
     * @return \Portal\ContentBundle\Entity\Slider
     */
    public function findOneById($id)
    {
        return $this->getSliderRepository()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Slider
     */
    public function find($id)
    {
        if ($id) {
            return $this->getSliderRepository()->find($id);
        } else {
            return $this->getSliderRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Slider[]
     */
    public function findAll()
    {
        return $this->getSliderRepository()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Slider
     */
    public function findOneBy($array)
    {
        return $this->getSliderRepository()->findOneBy($array);
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Slider
     */
    public function findBy($array)
    {
        return $this->getSliderRepository()->findBy($array);
    }

    /**
     *
     * @return array
     */
    public function getSliderByCode($slug)
    {
        return $this->getSliderRepository()->getSliderByCode($slug);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSliderBannersById($id)
    {
        return $this->getSliderRepository()->getSliderBannersById($id);
    }

    /**
     * @return array
     */
    public function getSliderList()
    {
        return $this->getSliderRepository()->getSliderList();
    }
}
