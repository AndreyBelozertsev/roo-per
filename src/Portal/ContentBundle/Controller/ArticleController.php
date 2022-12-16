<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Portal\AdminBundle\Form\CommentType;
use Portal\ContentBundle\Entity\Comment;
use Portal\HelperBundle\Helper\Pagination;
use Portal\ContentBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    public function showAction(Request $request)
    {
        $id = (int)$request->get('id');
        $objArticle = $this->get('article_manager')->find($id);
        if (!$objArticle instanceof Article || !$objArticle->getIsPublished() || $objArticle->getIsDeleted() ||
            !$objArticle->getCategory()->getIsPublished()) {

            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $arrParams['article'] = $objArticle;
        $em = $this->getDoctrine()->getManager();

        $news_shown_ids = explode(',', $request->cookies->get('news_shown_ids'));
        if (!in_array($id, $news_shown_ids)) {
            $objArticle->setViewsCounter((int)$objArticle->getViewsCounter() + 1);
            $em->persist($objArticle);
            $em->flush();
            $news_shown_ids[] = $id;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $arrParams['formOpened'] = true;
            if ($form->isValid()) {
                $objArticle->addComments($comment);
                $em->persist($objArticle);
                $em->flush();

                $arrParams['thanks'] = $this->get('translator')->trans('thanks_for_comment');
            }
        }

        $arrParams['form'] = $form->createView();

        $response = $this->render('PortalContentBundle:Article:show.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('news_shown_ids', implode(',', $news_shown_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    /**
     * Article/category page
     * @param Request $request
     * @param int $cat
     * @return Response
     * @throws \Twig_Error
     */
    public function viewAllAction(Request $request, $cat)
    {
        $category = $this->get('doctrine')->getRepository('PortalContentBundle:ArticleCategory')
            ->findBy(['id' => $cat, 'isPublished' => true]);
        if (!count($category)) {
            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $ignoreTime = date('Y-m-d H:i:s');
        $arrParams['ignoreTime'] = $ignoreTime;

        $articleRep = $this->get('doctrine')->getRepository('PortalContentBundle:Article');
        $articleCount = (int)$articleRep->getArticleCount($cat);
        $arrParams['article_count'] = $articleCount;

        $totalPages = ceil($articleCount / Article::PAGE_PAGINATION_LIMIT);
        $currentPage = abs($request->get('page')) ?: 1;
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $currentPage = ($currentPage == 0) ? 1 : $currentPage;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);
        $arrParams['articleList'] = $articleRep->getPaginatedList($cat, $currentPage - 1);
        $arrParams['category'] = $cat;

        return $this->render('PortalContentBundle:Article:depNews.html.twig', $arrParams);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMoreArticlesAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('PortalContentBundle:Article');
        $cat = (int)$request->get('cat');
        $page = (int)$request->get('page');

        $list = $this->render('PortalContentBundle:Article:articleList.html.twig', [
            'articleList' => $em->getPaginatedList($cat, $page),
        ])->getContent();
        $page++;

        $countArticle = $em->getArticleCount($cat);
        $shown = Article::PAGE_PAGINATION_LIMIT * ($page);
        $totalPages = ceil($countArticle / Article::PAGE_PAGINATION_LIMIT);

        $pagination = new Pagination($this->container);
        $paginationRender = $pagination->render($page, $totalPages, 'view_all_articles', ['cat' => $cat]);

        return new JsonResponse([
            'list' => $list,
            'page' => $page,
            'hideButton' => ($shown >= $countArticle),
            'pagination' => $paginationRender
        ]);
    }
}
