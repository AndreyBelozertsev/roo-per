<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Portal\ContentBundle\Entity\Material;
use Portal\AdminBundle\Form\MaterialFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Portal\HelperBundle\Helper\PortalHelper;

class MaterialAdminControlerController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $listMaterial = $this->get('customer_material_manager')->findAll();
        $adapter = new ArrayAdapter($listMaterial);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:MaterialAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $material = new Material();
            $permissionCode = 'create_material';
            $isAuthor = false;
            $validation_group = ['new'];
        } else {
            // edit
            $material = $this->get('customer_material_manager')->findOneBy(['id' => $id]);
            if (!$material instanceof Material) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = 'edit_material';
            $isAuthor = ($material->getAuthor() === $currentUserId);
            $validation_group = ['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(MaterialFormType::class, $material, [
            'validation_groups' => $validation_group,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $material->setAuthor($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($material);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_material_edit', [
                        'id' => $material->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:MaterialAdmin:edit.html.twig', [
            'material' => $material,
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $material = $this->get('customer_material_manager')->findOneBy(['id' => $id]);
        if ($material instanceof Material) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted('delete_material', $instanceCode, $currentUserId);
            $isAuthor = ($material->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($material);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_material_view_all', [
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
