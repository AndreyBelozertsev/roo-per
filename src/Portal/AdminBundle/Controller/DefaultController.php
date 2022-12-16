<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PortalAdminBundle:Default:index.html.twig');
    }

    public function adminAction()
    {
        return $this->render('PortalAdminBundle:Default:index.html.twig', ['admin' => true]);
    }

    public function instanceAction($instanceCode)
    {
        $currentUser = $this->get('user_helper')->getCurrentUser();

//        $grantedInstances = $this->get('user_role_manager')->findGrantedInstancesByUserId($currentUser->getId());
//        $grantedInstances = array_column($grantedInstances, 'instance_id');
        $isSuperAdmin = ($currentUser->getRoles()[0] === 'ROLE_SUPER_ADMIN');
//        $instanceId = $this->get('instance_manager')->findOneBy(['code' => $instanceCode])->getId();

        if ($isSuperAdmin) {

            return $this->render('PortalAdminBundle:Default:index.html.twig', [
                'admin' => true
            ]);
        } else {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }
    }
}
