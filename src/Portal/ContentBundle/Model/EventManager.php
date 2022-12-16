<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventManager
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
    private function getEventRepo()
    {
        return $this->em->getRepository('PortalContentBundle:Event');
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
     * @return \Portal\ContentBundle\Entity\Event
     */
    public function findOneById($id)
    {
        return $this->getEventRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\Event
     */
    public function find($id)
    {
        return $id ? $this->getEventRepo()->find($id) : $this->getEventRepo()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\Event[]
     */
    public function findAll()
    {
        return $this->getEventRepo()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Event
     */
    public function findOneBy($array)
    {
        return $this->getEventRepo()->findOneBy($array);
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Event[]
     */
    public function findBy($array, $orderBy = null)
    {
        return $this->getEventRepo()->findBy($array, $orderBy);
    }

    /**
     *
     * @return array
     */
    public function getEventListByDate($date = null)
    {
        return $this->getEventRepo()->getEventListByDate($date);
    }

    /**
     *
     * @return array
     */
    public function getEventListDayOnMonth($date = null)
    {
        return $this->getEventRepo()->getEventListDayOnMonth($date);
    }

    /**
     * @param string $dateStart
     * @param string $dateEnd
     * @param integer $page
     * @param integer $limit
     *
     * @return mixed
     */
    public function getActualEventList($dateStart = null, $dateEnd = null, $page = null, $limit = null)
    {
        return $this->getEventRepo()->getActualEventList($dateStart, $dateEnd, $page, $limit);
    }

    /**
     * @param string $dateStart
     * @param string $dateEnd
     *
     * @return integer
     */
    public function getActualEventCount($dateStart = null, $dateEnd = null)
    {
        return $this->getEventRepo()->getActualEventCount($dateStart, $dateEnd);
    }

    /**
     * @param integer $minId
     *
     * @return array
     */
    public function getAllEventsForSearchGrab($minId = 0)
    {
        return $this->getEventRepo()->getAllEventsForSearchGrab($minId);
    }

    /**
     * @param integer $maxId
     *
     * @return array
     */
    public function getAllEventsForSearchUpdate($maxId = 0)
    {
        return $this->getEventRepo()->getAllEventsForSearchUpdate($maxId);
    }
    
    /**
     * @param array $indexedEventsId
     * @return bool
     */
    public function updateIsSearchIndexedFlag($indexedEventsId)
    {
        return $this->getEventRepo()->updateIsSearchIndexedFlag($indexedEventsId);
    }

    public function getAllVisualEvents()
    {
        return $this->getEventRepo()->getAllVisualEvents();
    }
}
