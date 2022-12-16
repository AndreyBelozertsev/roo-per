<?php

namespace Portal\HelperBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;;

class AccessLogManager
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
     * @return \Portal\HelperBundle\Repository\AccessLogMessageRepository
     */
    private function getRepo()
    {
        return $this->em->getRepository('PortalHelperBundle:AccessLogMessage');
    }

    /**
     * Creates log message
     *
     * @param int $userId
     * @param int $timestamp
     * @param string $clientIp
     * @param string $userAgent
     * @param string $uri
     * @param string $controllerName
     * @param string $actionName
     * @param string $requestData
     * @param int $statusCode
     * @param string $responseMessage
     *
     * @return bool
     */
    public function createMessage(
        $userId,
        $timestamp,
        $clientIp,
        $userAgent,
        $uri,
        $controllerName,
        $actionName,
        $requestData,
        $statusCode,
        $responseMessage
    )
    {
        return $this->getRepo()->createMessage($userId, $timestamp, $clientIp, $userAgent, $uri, $controllerName,
            $actionName, $requestData, $statusCode, $responseMessage);
    }

    /**
     * @param $userId
     * @return array
     */
    public function getLastUrls($userId)
    {
        return $this->getRepo()->getLastUrls($userId);
    }
}
