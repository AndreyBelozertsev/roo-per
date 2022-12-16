<?php

namespace Portal\HelperBundle\Helper;

use Portal\HelperBundle\Model\AccessLogManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalLogger
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AccessLogManager
     */
    private $messageMgr;
    
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $tokenStorage
     * @param AccessLogManager $messageMgr
     * @param ContainerInterface $container
     */
    public function __construct(TokenStorage $tokenStorage, AccessLogManager $messageMgr, ContainerInterface $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->messageMgr = $messageMgr;
        $this->container = $container;
    }

    /**
     * Parses required data from request and response objects
     *
     * @param Request $request
     * @param Response $response
     * @param string $responseMessage
     */
    public function log(Request $request, Response $response, $responseMessage = '')
    {
        // not logging preflight requests
        if($request->getMethod() == 'OPTIONS')
            return;

        
        if ($this->container->hasParameter('admin_prefix') &&
                strpos($request->getRequestUri(), $this->container->getParameter('admin_prefix')) !== false) {
            
            $userId = null;
            if(is_object($this->tokenStorage->getToken())) {
                $user = $this->tokenStorage->getToken()->getUser();
                $userId = is_object($user) ? $user->getId() : null;
            }

            // Split controller and method names
            $controllerString = explode('::', substr($request->attributes->get('_controller'),
                strrpos($request->attributes->get('_controller'), '\\') + 1));

            $controllerName = null;
            $methodName = null;
            if(is_array($controllerString) && count($controllerString) > 1) {
                $controllerName = $controllerString[0];
                $methodName = $controllerString[1];
            }

            // Decode response message, if required
//            $responseMessage = '';
            if($response->getStatusCode() !== 200) {
                $responseContent = json_decode($response->getContent());
                if(isset($responseContent->message)) {
                    $responseMessage = $responseContent->message;
                }
            }

            $this->messageMgr->createMessage(
                $userId,
                (new \DateTime())->format('d.m.Y H:i:s'),
                $request->getClientIp(),
                $request->headers->get('User-Agent'),
                $request->getRequestUri(),
                $controllerName,
                $methodName,
                json_encode($request->request->all()),
                $response->getStatusCode(),
                $responseMessage
            );
        }
    }

}