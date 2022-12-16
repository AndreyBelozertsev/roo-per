<?php

namespace Portal\HelperBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;;

class LogManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Portal\HelperBundle\Repository\LogRepository
     */
    private function getRepo()
    {
        return $this->em->getRepository('PortalHelperBundle:Log');
    }

    /**
     * Creates log message
     *
     * @param int $userId
     * @param int $timestamp
     * @param string $entityType
     * @param int $entityId
     * @param string $actionType
     * @param string $message
     * @param string $instanceCode
     *
     * @return bool
     */
    public function createMessage(
        $userId,
        $timestamp,
        $entityType,
        $entityId,
        $actionType,
        $message,
        $instanceCode
    )
    {
        return $this->getRepo()->createMessage($userId, $timestamp, $entityType, $entityId, $actionType,
            $message, $instanceCode);
    }
}
