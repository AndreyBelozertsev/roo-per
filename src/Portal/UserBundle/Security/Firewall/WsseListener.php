<?php

namespace Portal\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Portal\UserBundle\Security\Authentication\Token\WsseUserToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class WsseListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(TokenStorage $securityContext, AuthenticationManagerInterface $authenticationManager, $router)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->container = $router;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if($event->getRequest()->getMethod() == 'OPTIONS') {
            $this->handleOptions($event);
            return;
        }

        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';
        if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
            $this->forbid($event);
            return;
        }

        $token = new WsseUserToken();
        $token->setUser($matches[1]);

        $token->digest   = $matches[2];
        $token->nonce    = $matches[3];
        $token->created  = $matches[4];

        try {
            $authToken = $this->authenticationManager->authenticate($token); 
            $this->securityContext->setToken($authToken);
            return;
        } catch (AuthenticationException $failed) {
            $this->forbid($event);
        }
    }

    private function handleOptions(GetResponseEvent $event)
    {
        $response = new Response();
        $router = $this->container->get('router');
        $route = $router->getRouteCollection()->get($event->getRequest()->get('_route'));

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $route->getMethods()));
        $response->headers->set('Access-Control-Allow-Headers', 'accept, x-wsse, content-type');
        $event->setResponse($response);
    }

    private function forbid(GetResponseEvent $event)
    {
        $response = new Response();
        $response->setStatusCode(403);

        $event->setResponse($response);
    }
}
