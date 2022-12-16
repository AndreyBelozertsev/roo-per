<?php

namespace Portal\AdminBundle\Controller;

use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Portal\ContentBundle\Entity\Post;
use Portal\AdminBundle\Form\PostType;

class PostAdminController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        if (!$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $em = $this->getDoctrine()->getManager();
        $adapter = new ArrayAdapter($em->getRepository('PortalContentBundle:Post')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:PostAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('admin_instance_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('PortalAdminBundle:PostAdmin:create.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('PortalContentBundle:Post')->findOneBy(['id' => $request->get('id')]);
        if (!$post instanceof Post) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_instance_post_view_all');
        }

        return $this->render('PortalAdminBundle:PostAdmin:edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('PortalContentBundle:Post')->findOneBy(['id' => $request->get('id')]);
        if ($post instanceof Post) {
            $post->setIsDeleted(true);
            $em->persist($post);
            $em->flush();

            $page = [];
            if ($request->query->get('page') !== null) {
                $page['page'] = (int)$request->query->get('page');
            }
            $response['redirectUrl'] = $this->get('router')->generate('admin_instance_post_view_all', $page);
            $response['status'] = true;
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function restoreAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('PortalContentBundle:Post')->findOneBy(['id' => $id]);
        if ($post instanceof Post) {
            $post->setIsDeleted(false);
            $em->persist($post);
            $em->flush();

            $response['status'] = true;
            $response['reload'] = true;

        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
