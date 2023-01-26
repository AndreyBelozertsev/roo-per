<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\Pagination;

class SearchController extends Controller
{
    /**
     * Search page.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function searchAction(Request $request)
    {
        $queryStr = $request->query->get('str');
        if ($queryStr) {
            $rep = $this->getDoctrine()->getRepository('PortalContentBundle:Article');
            $articleCount = $rep->countSearchArticle($queryStr);
            $totalPages = (int)ceil($articleCount / Article::PAGE_PAGINATION_LIMIT);
            $currentPage = abs($request->get('page')) ?: 1;
            $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
            $currentPage = ($currentPage == 0) ? 1 : $currentPage;
            $arrParams = [
                'articleList' => $rep->searchArticle($queryStr, $currentPage - 1),
                'pagination' => (new Pagination($this->container))->render($currentPage, $totalPages),
                'hideButton' => ($currentPage >= $totalPages),
                'currentPage' => $currentPage
            ];
        } else {
            $arrParams = [
                'articleList' => [],
                'pagination' => '',
                'hideButton' => true
            ];
        }

        return $this->render('PortalContentBundle:Article:search.html.twig', $arrParams);
    }

    // /**
    //  * Show more results button.
    //  *
    //  * @param Request $request
    //  * @return JsonResponse
    //  * @throws \Doctrine\DBAL\DBALException
    //  */
    // public function getMoreResultsAction(Request $request)
    // {
    //     $queryStr = $request->get('str');
    //     $page = (int)$request->get('page');

    //     $rep = $this->getDoctrine()->getRepository('PortalContentBundle:Article');

    //     $list = $this->render('PortalContentBundle:Article:articleList.html.twig', [
    //         'articleList' => $rep->searchArticle($queryStr, $page),
    //     ])->getContent();
    //     $page++;

    //     $articleCount = $rep->countSearchArticle($queryStr);
    //     $shown = Article::PAGE_PAGINATION_LIMIT * ($page);
    //     $totalPages = ceil($articleCount / Article::PAGE_PAGINATION_LIMIT);

    //     $pagination = new Pagination($this->container);
    //     $paginationRender = $pagination->render($page, $totalPages, 'search', ['str' => $queryStr]);

    //     return new JsonResponse([
    //         'list' => $list,
    //         'page' => $page,
    //         'hideButton' => ($shown >= $articleCount),
    //         'pagination' => $paginationRender
    //     ]);
    // }
}
