<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Portal\ContentBundle\Entity\SliderToBanner;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SliderToBannerManager
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
    private function getSliderToBannerRepository()
    {
        return $this->em->getRepository('PortalContentBundle:SliderToBanner');
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
     * @return mixed
     */
    public function getUsedBannerIds($id)
    {
        return $this->getSliderToBannerRepository()->getUsedBannerIds($id);
    }

    /**
     * @param string $orderIds
     * @param int $sliderId
     */
    public function setBannersToSlider($orderIds, $sliderId)
    {
        $this->getSliderToBannerRepository()->setBannersToSlider($orderIds, $sliderId);
    }
}
