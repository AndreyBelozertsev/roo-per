<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->get('doctrine');

        $newMagazine = $em->getRepository('PortalContentBundle:MagazineNewspaper')->getLastMagazine();

        $magazineArticleList = [];

        if($newMagazine){
            $magazineArticleList = $em->getRepository('PortalContentBundle:MagazineArticle')->getMagazineArticleList($newMagazine['id']);
        }

        return $this->render('PortalContentBundle:Default:index.html.twig', [
            'newMagazine' => $newMagazine,
            'magazineArticleList' => $magazineArticleList
        ]);
    }
}
