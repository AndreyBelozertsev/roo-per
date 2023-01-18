<?php

namespace Portal\ContentBundle\Controller;

use Portal\AdminBundle\Form\CommentType;
use Portal\ContentBundle\Entity\Comment;
use Portal\HelperBundle\Helper\Pagination;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Portal\ContentBundle\Entity\MagazineArticle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MagazineArticleController extends Controller
{
    public function showAction(Request $request)
    {
        // $id = (int)$request->get('id');
        // $objArticle = $this->get('magazine_article_manager')->find($id);
        // if (!$objArticle instanceof MagazineArticle || !$objArticle->getIsPublished() || $objArticle->getIsDeleted() ||
        //     !$objArticle->getMagazine()->getIsPublished()) {

        //     throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        // }

        // $arrParams['magazine_article'] = $objArticle;
        // $em = $this->getDoctrine()->getManager();

        // $magazine_article_shown_ids = explode(',', $request->cookies->get('magazine_article_shown_ids'));
        // if (!in_array($id, $magazine_article_shown_ids)) {
        //     $objArticle->setViewsCounter((int)$objArticle->getViewsCounter() + 1);
        //     $em->persist($objArticle);
        //     $em->flush();
        //     $magazine_article_shown_ids[] = $id;
        // }

        // $response = $this->render('PortalContentBundle:MagazineArticle:show.html.twig', $arrParams);
        // $response->headers->setCookie(
        //     new Cookie('magazine_article_shown_ids', implode(',', $magazine_article_shown_ids), strtotime('now + 3 months'))
        // );

        // return $response;



        $id = (int)$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $magazineArticle = $em->getRepository('PortalContentBundle:MagazineArticle')->findOneBy([
            'id' => $id,
            'isPublished' => true,
            'isDeleted' => false
        ]);

        if (!$magazineArticle instanceof MagazineArticle) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $magazineArticle_shown_ids = explode(',', $request->cookies->get('magazineArticle_shown_ids'));
        if (!in_array($id, $magazineArticle_shown_ids)) {
            $magazineArticle->setViewsCounter((int)$magazineArticle->getViewsCounter() + 1);
            $em->persist($magazineArticle);
            $em->flush();
            $magazineArticle_shown_ids[] = $id;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $arrParams['formOpened'] = true;
            if ($form->isValid())  {
                $magazineArticle->addComments($comment);
                $em->persist($magazineArticle);
                $em->flush();

                $arrParams['thanks'] = $this->get('translator')->trans('thanks_for_comment');
            }
        }

        $arrParams['magazineArticle'] = $magazineArticle;
        $arrParams['form'] = $form->createView();
        $arrParams['comments'] = $em->getRepository('PortalContentBundle:Comment')->findBy([
            'magazineArticle' => $id,
            'isPublished' => true
        ]);

        $response = $this->render('PortalContentBundle:MagazineArticle:show.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('magazineArticle_shown_ids', implode(',', $magazineArticle_shown_ids), strtotime('now + 3 months'))
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
