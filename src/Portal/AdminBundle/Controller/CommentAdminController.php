<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\CommentModerateType;
use Portal\ContentBundle\Entity\Comment;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Comment controller.
 */
class CommentAdminController extends Controller
{
    /**
     * Lists all comments.
     *
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $adapter = new ArrayAdapter($em->getRepository('PortalContentBundle:Comment')->getComments());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('@PortalAdmin/comment/index.html.twig', [
            'pagerFanta' => $pagerfanta
        ]);
    }

    /**
     * Creates a new comment entity.
     *
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
//    public function newAction(Request $request)
//    {
//        $comment = new Comment();
//        $form = $this->createForm(CommentType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($comment);
//            $em->flush();
//
//            return $this->redirectToRoute('comment_show', ['id' => $comment->getId()]);
//        }
//
//        return $this->render('@PortalAdmin/comment/new.html.twig', [
//            'comment' => $comment,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * Finds and displays a comment entity.
     *
     * @Method("GET")
     * @param Comment $comment
     * @return Response
     */
//    public function showAction(Comment $comment)
//    {
//        $deleteForm = $this->createDeleteForm($comment);
//
//        return $this->render('@PortalAdmin/comment/show.html.twig', [
//            'comment' => $comment,
//            'delete_form' => $deleteForm->createView(),
//        ]);
//    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Comment $comment
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Comment $comment)
    {
        $editForm = $this->createForm(CommentModerateType::class, $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_admin_comment_viewall');
        }

        return $this->render('@PortalAdmin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $editForm->createView()
        ]);
    }

    /**
     * Deletes a comment entity.
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        if ($comment instanceof Comment) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();

            $page = [];
            if ($request->query->get('page') !== null) {
                $page['page'] = (int)$request->query->get('page');
            }
            $response['redirectUrl'] = $this->get('router')->generate('admin_admin_comment_viewall', $page);
            $response['status'] = true;

        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    /**
     * Creates a form to delete a comment entity.
     *
     * @param Comment $comment The comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(Comment $comment)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('admin_admin_comment_remove', ['id' => $comment->getId()]))
//            ->setMethod('DELETE')
//            ->getForm();
//    }
}
