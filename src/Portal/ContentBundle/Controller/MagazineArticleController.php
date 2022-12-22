<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Portal\HelperBundle\Helper\Pagination;
use Portal\ContentBundle\Entity\MagazineArticle;
use Symfony\Component\HttpFoundation\Response;

class MagazineArticleController extends Controller
{
    public function showAction(Request $request)
    {
        $id = (int)$request->get('id');
        $objArticle = $this->get('magazine_article_manager')->find($id);
        if (!$objArticle instanceof MagazineArticle || !$objArticle->getIsPublished() || $objArticle->getIsDeleted() ||
            !$objArticle->getMagazine()->getIsPublished()) {

            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $arrParams['magazine_article'] = $objArticle;
        $em = $this->getDoctrine()->getManager();

        $magazine_article_shown_ids = explode(',', $request->cookies->get('magazine_article_shown_ids'));
        if (!in_array($id, $magazine_article_shown_ids)) {
            $objArticle->setViewsCounter((int)$objArticle->getViewsCounter() + 1);
            $em->persist($objArticle);
            $em->flush();
            $magazine_article_shown_ids[] = $id;
        }

        $response = $this->render('PortalContentBundle:MagazineArticle:show.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('magazine_article_shown_ids', implode(',', $magazine_article_shown_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    /**
     * MagazineArticle/magazine page
     * @param Request $request
     * @param int $magazine
     * @return Response
     * @throws \Twig_Error
     */
    public function viewAllAction(Request $request, $magazine)
    {
        $magazine = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineNewspaper')
            ->findBy(['id' => $magazine, 'isPublished' => true]);
        if (!count($magazine)) {
            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $ignoreTime = date('Y-m-d H:i:s');
        $arrParams['ignoreTime'] = $ignoreTime;

        $magazineArticleRep = $this->get('doctrine')->getRepository('PortalContentBundle:MagazineArticle');
        $magazineArticleCount = (int)$magazineArticleRep->getMagazineArticleCount($magazine);
        $arrParams['magazine_article_count'] = $magazineArticleCount;

        $totalPages = ceil($magazineArticleCount / MagazineArticle::PAGE_PAGINATION_LIMIT);
        $currentPage = abs($request->get('page')) ?: 1;
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $currentPage = ($currentPage == 0) ? 1 : $currentPage;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);
        $arrParams['magazineArticleList'] = $articleRep->getPaginatedList($magazine, $currentPage - 1);
        $arrParams['magazine'] = $magazine;

        return $this->render('PortalContentBundle:MagazineArticle:depNews.html.twig', $arrParams);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMoreMagazineArticlesAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('PortalContentBundle:MagazineArticle');
        $magazine = (int)$request->get('magazine');
        $page = (int)$request->get('page');

        $list = $this->render('PortalContentBundle:MagazineArticle:magazineArticleList.html.twig', [
            'magazineArticleList' => $em->getPaginatedList($magazine, $page),
        ])->getContent();
        $page++;

        $countMagazineArticle = $em->getArticleCount($magazine);
        $shown = MagazineArticle::PAGE_PAGINATION_LIMIT * ($page);
        $totalPages = ceil($countMagazineArticle / MagazineArticle::PAGE_PAGINATION_LIMIT);

        $pagination = new Pagination($this->container);
        $paginationRender = $pagination->render($page, $totalPages, 'view_all_magazine_articles', ['magazine' => $magazine]);

        return new JsonResponse([
            'list' => $list,
            'page' => $page,
            'hideButton' => ($shown >= $countArticle),
            'pagination' => $paginationRender
        ]);
    }
}
