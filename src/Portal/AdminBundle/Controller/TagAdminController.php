<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Tag;
use Portal\AdminBundle\Form\TagFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class TagAdminController extends Controller
{
    public function indexAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Tag::PERMISSIONS_TAG);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $adapter = new ArrayAdapter($this->get('customer_tag_manager')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(Tag::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:TagAdmin:index.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $tag = new Tag();
            $permissionCode = Tag::PERMISSIONS_TAG['create'];
        } else {
            // edit
            $tag = $this->get('customer_tag_manager')->findOneById($id);
            if (!$tag instanceof Tag) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = Tag::PERMISSIONS_TAG['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                try {
                    $em->persist($tag);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_tag_index', ['instanceCode' => $instanceCode]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:TagAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $tag = $this->get('customer_tag_manager')->find($id);
        if ($tag instanceof Tag) {
            $isGranted = $this->get('user_manager')->isGranted(
                Tag::PERMISSIONS_TAG['delete'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($tag);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_tag_index', [
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('can_not_delete');
        }

        return new JsonResponse($response);
    }
}
