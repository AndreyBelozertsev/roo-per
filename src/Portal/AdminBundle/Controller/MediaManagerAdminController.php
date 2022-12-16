<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaManagerAdminController extends Controller
{
    public function viewAction()
    {
        $arrParam = [];
        $arrParam['conf'] = 'media';
        return $this->render('PortalAdminBundle:MediaManagerAdmin:view.html.twig', $arrParam);
    }
    public function viewAllAction()
    {
        $arrParam = [];
        $arrParam['conf'] = 'all_media';
        return $this->render('PortalAdminBundle:MediaManagerAdmin:view.html.twig', $arrParam);
    }
}
