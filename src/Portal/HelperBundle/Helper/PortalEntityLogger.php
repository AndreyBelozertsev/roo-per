<?php

namespace Portal\HelperBundle\Helper;

use Portal\HelperBundle\Model\LogManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PortalEntityLogger
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var LogManager
     */
    private $messageMgr;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $tokenStorage
     * @param LogManager $messageMgr
     * @param ContainerInterface $container
     */
    public function __construct(TokenStorage $tokenStorage, LogManager $messageMgr, ContainerInterface $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->messageMgr = $messageMgr;
        $this->container = $container;
    }

    /**
     * Parses required data from request and response objects
     *
     * @param string $entityType
     * @param int $entityId
     * @param string $actionType
     * @param string $instanceCode
     * @param string $message
     * @param array $changeList
     */
    public function log($entityType, $entityId, $actionType, $instanceCode, $message = null, $changeList = null)
    {
        $methodName = null;
        if ($changeList) {
            if (array_key_exists('isDeleted', $changeList)) {
                $methodName = " (isDeleted: " . $changeList['isDeleted'][1] . ")";
//            } else {
//                $message .= "\n\r Изменения: ";
//                foreach ($changeList as $key => $value) {
//                    $message .= "\n\r $key: \n\r\tДо - $value[0], \n\r\tПосле - $value[1]; ";
//                    $message .= "\n\r $key; ";
//                }
            }
        }

        $userId = null;
        if (is_object($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
            $userId = is_object($user) ? $user->getId() : null;
        }

        $this->messageMgr->createMessage(
            $userId,
            (new \DateTime())->format('d.m.Y H:i:s'),
            $entityType,
            $entityId,
            $actionType . $methodName,
            $message,
            $instanceCode
        );
    }

}