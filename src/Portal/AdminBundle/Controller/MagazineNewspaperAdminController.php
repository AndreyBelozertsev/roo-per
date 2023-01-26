<?php

namespace Portal\AdminBundle\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Portal\AdminBundle\Form\MagazineNewspaperType;
use Portal\ContentBundle\Entity\MagazineNewspaper;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class MagazineNewspaperAdminController extends Controller
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
        $adapter = new ArrayAdapter($em->getRepository('PortalContentBundle:MagazineNewspaper')->findBy([], ['id'=>'DESC']));
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:MagazineNewspaperAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $magazine_newspaper = new MagazineNewspaper();
        $form = $this->createForm(MagazineNewspaperType::class, $magazine_newspaper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($magazine_newspaper);
            $em->flush();

            return $this->redirectToRoute('admin_instance_magazine_newspaper_edit', ['id' => $magazine_newspaper->getId()]);
        }

        return $this->render('PortalAdminBundle:MagazineNewspaperAdmin:create.html.twig', [
            'magazine_newspaper' => $magazine_newspaper,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $magazine_newspaper = $em->getRepository('PortalContentBundle:MagazineNewspaper')->findOneBy(['id' => $request->get('id')]);
        if (!$magazine_newspaper instanceof MagazineNewspaper) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        $form = $this->createForm(MagazineNewspaperType::class, $magazine_newspaper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_instance_magazine_newspaper_view_all');
        }

        return $this->render('PortalAdminBundle:MagazineNewspaperAdmin:edit.html.twig', [
            'magazine_newspaper' => $magazine_newspaper,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $magazine_newspaper = $em->getRepository('PortalContentBundle:MagazineNewspaper')->findOneBy(['id' => $request->get('id')]);
        if ($magazine_newspaper instanceof MagazineNewspaper) {
            $magazine_newspaper->setIsDeleted(true);
            $em->persist($magazine_newspaper);
            $em->flush();

            $page = [];
            if ($request->query->get('page') !== null) {
                $page['page'] = (int)$request->query->get('page');
            }
            $response['redirectUrl'] = $this->get('router')->generate('admin_instance_magazine_newspaper_view_all', $page);
            $response['status'] = true;
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function restoreAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $magazine_newspaper = $em->getRepository('PortalContentBundle:MagazineNewspaper')->findOneBy(['id' => $id]);
        if ($magazine_newspaper instanceof MagazineNewspaper) {
            $magazine_newspaper->setIsDeleted(false);
            $em->persist($magazine_newspaper);
            $em->flush();

            $response['status'] = true;
            $response['reload'] = true;

        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
