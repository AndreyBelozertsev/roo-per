<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Portal\ContentBundle\Entity\Post;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->get('doctrine');
        
        return $this->render('PortalContentBundle:Default:index.html.twig', [
            'opinionList' => $em->getRepository('PortalContentBundle:Post')->findBy(
                ['isPublished' => true, 'isDeleted' => false],
                ['createdAt' => 'DESC'],
                Post::HOME_PAGE_LIMIT
            ),
            'slider' => $em->getRepository('PortalContentBundle:Article')->getShowInSlider()
        ]);
    }
}
