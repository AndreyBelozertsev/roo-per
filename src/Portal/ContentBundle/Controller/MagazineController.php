<?php

namespace Portal\ContentBundle\Controller;


use Portal\ContentBundle\Entity\MagazineNewspaper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Portal\HelperBundle\Helper\Pagination;

class MagazineController extends Controller
{
    /**
     * View-all page
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $currentPage = (int)$request->get('page') ?: 1;
        $magazineRep = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineNewspaper');
        
        $arrParams['magazineList'] = $magazineRep->getPaginatedList($currentPage - 1, 'magazine');

        $totalPages = ceil($magazineRep->getCount('magazine') / MagazineNewspaper::PAGE_PAGINATION_LIMIT);
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $currentPage = ($currentPage == 0) ? 1 : $currentPage;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);

        return $this->render('PortalContentBundle:Magazine:index.html.twig', $arrParams);
    }

    // /**
    //  * @param Request $request
    //  * @return JsonResponse
    //  */
    // public function getMoreMagazinesAction(Request $request)
    // {
    //     $currentPage = (int)$request->get('page') ?: 1;
    //     $magazineRep = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineNewspaper');

    //     $totalPages = ceil($magazineRep->getCount('magazine') / MagazineNewspaper::PAGE_PAGINATION_LIMIT);

    //     $arrParams['magazineList'] = $this->render('PortalContentBundle:Magazine:magazineList.html.twig', [
    //         'magazineList' => $magazineRep->getPaginatedList($currentPage, 'magazine'),
    //     ])->getContent();

    //     $currentPage++;
    //     $arrParams['page'] = $currentPage;
    //     $arrParams['hideButton'] = ($currentPage >= $totalPages);

    //     $pagination = new Pagination($this->container);
    //     $arrParams['pagination'] = $pagination->render($currentPage, $totalPages, 'magazine');

    //     return new JsonResponse($arrParams);
    // }

    /**
     * Show page
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $magazine = $em->getRepository('PortalContentBundle:MagazineNewspaper')->findOneBy([
            'id' => $id,
            'type_of' => 'magazine',
            'isPublished' => true,
            'isDeleted' => false
        ]);

        if (!$magazine instanceof MagazineNewspaper) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $arrParams['magazine'] = $magazine;

        $arrParams['magazineArticleList'] = $em->getRepository('PortalContentBundle:MagazineArticle')->getMagazineArticleList($magazine->getId());

        $response = $this->render('PortalContentBundle:Magazine:show.html.twig', $arrParams);

        return $response;
    }
}
