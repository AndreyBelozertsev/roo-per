<?php

namespace Portal\ContentBundle\Controller;

use Portal\AdminBundle\Form\CommentType;
use Portal\ContentBundle\Entity\Comment;
use Portal\ContentBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Portal\HelperBundle\Helper\Pagination;

class PostController extends Controller
{
    /**
     * View-all page
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $currentPage = (int)$request->get('page') ?: 1;
        $postRep = $this->get('doctrine')->getRepository('PortalContentBundle:Post');

        $arrParams['postList'] = $postRep->getPaginatedList($currentPage - 1);

        $totalPages = ceil($postRep->getPostCount() / Post::PAGE_PAGINATION_LIMIT);
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $currentPage = ($currentPage == 0) ? 1 : $currentPage;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);

        return $this->render('PortalContentBundle:Post:index.html.twig', $arrParams);
    }

    /**
     * Show page
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('PortalContentBundle:Post')->findOneBy([
            'id' => $id,
            'isPublished' => true,
            'isDeleted' => false
        ]);

        if (!$post instanceof Post) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $post_shown_ids = explode(',', $request->cookies->get('post_shown_ids'));
        if (!in_array($id, $post_shown_ids)) {
            $post->setViewsCounter((int)$post->getViewsCounter() + 1);
            $em->persist($post);
            $em->flush();
            $post_shown_ids[] = $id;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $arrParams['formOpened'] = true;
            if ($form->isValid())  {
                $post->addComments($comment);
                $em->persist($post);
                $em->flush();

                $arrParams['thanks'] = $this->get('translator')->trans('thanks_for_comment');
            }
        }

        $arrParams['post'] = $post;
        $arrParams['form'] = $form->createView();
        $arrParams['comments'] = $em->getRepository('PortalContentBundle:Comment')->findBy([
            'post' => $id,
            'isPublished' => true
        ]);

        $response = $this->render('PortalContentBundle:Post:show.html.twig', $arrParams);
        $response->headers->setCookie(
            new Cookie('post_shown_ids', implode(',', $post_shown_ids), strtotime('now + 3 months'))
        );

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPostsAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('PortalContentBundle:Post');
        $currentPage = (int)$request->request->get('page') ?: 1;

        $totalPages = ceil($em->getPostCount() / Post::PAGE_PAGINATION_LIMIT);

        $arrParams['postList'] = $this->render('PortalContentBundle:Post:postList.html.twig', [
            'postList' => $em->getPaginatedList($currentPage),
        ])->getContent();

        $currentPage++;
        $arrParams['page'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);

        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages, 'post');

        return new JsonResponse($arrParams);
    }
}
