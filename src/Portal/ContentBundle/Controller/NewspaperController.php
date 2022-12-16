<?php

namespace Portal\ContentBundle\Controller;


use Portal\ContentBundle\Entity\MagazineNewspaper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Portal\HelperBundle\Helper\Pagination;

class NewspaperController extends Controller
{
    /**
     * View-all page
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $currentPage = (int)$request->get('page') ?: 1;
        $newspaperRep = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineNewspaper');
        
        $arrParams['newspaperList'] = $newspaperRep->getPaginatedList($currentPage - 1, 'newspaper');

        $totalPages = ceil($newspaperRep->getCount('newspaper') / MagazineNewspaper::PAGE_PAGINATION_LIMIT);
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $currentPage = ($currentPage == 0) ? 1 : $currentPage;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);

        return $this->render('PortalContentBundle:Newspaper:index.html.twig', $arrParams);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMoreNewspapersAction(Request $request)
    {
        $currentPage = (int)$request->get('page') ?: 1;
        $newspaperRep = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineNewspaper');

        $totalPages = ceil($newspaperRep->getCount('newspaper') / MagazineNewspaper::PAGE_PAGINATION_LIMIT);

        $arrParams['newspaperList'] = $this->render('PortalContentBundle:Newspaper:newspaperList.html.twig', [
            'newspaperList' => $newspaperRep->getPaginatedList($currentPage, 'newspaper'),
        ])->getContent();

        $currentPage++;
        $arrParams['page'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages, 'newspaper');

        return new JsonResponse($arrParams);
    }
}