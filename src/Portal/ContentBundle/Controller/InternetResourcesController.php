<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InternetResourcesController extends Controller
{
    public function indexAction()
    {

        return $this->render('PortalContentBundle:InternetResources:show.html.twig', [
            'resources' => $this->get('customer_internet_resources_manager')->findAll()
        ]);
    }
}
