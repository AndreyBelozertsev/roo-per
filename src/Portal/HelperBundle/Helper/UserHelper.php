<?php

namespace Portal\HelperBundle\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use FOS\UserBundle\Model\User as FOSUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserHelper
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getCurrentUser()
    {
        if ($this->container->get('security.token_storage')->getToken() === null) {
//            $resultResponse['status'] = false;
//            $resultResponse['message'] = $this->container->get('translator')->trans('please_login', [], 'messages');
            echo new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof FOSUser) {
//            $resultResponse['status'] = false;
//            $resultResponse['message'] = $this->container->get('translator')->trans('please_login', [], 'messages');
            echo new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }

        return $user;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    private function generateUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
