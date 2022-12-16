<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Banner;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\BannerFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class BannerAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Banner::PERMISSIONS_BANNER);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $listBanner = $this->get('customer_banner_manager')->findAll();
        $adapter = new ArrayAdapter($listBanner);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:BannerAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $request->query->all()
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $banner = new Banner();
            $permissionCode = Banner::PERMISSIONS_BANNER['create'];
            $isAuthor = false;
            $validation_group = ['new'];
        } else {
            // edit
            $banner = $this->get('customer_banner_manager')->findOneById($id);
            if (!$banner instanceof Banner) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = Banner::PERMISSIONS_BANNER['edit'];
            $isAuthor = ($banner->getAuthor() === $currentUserId);
            $validation_group = ['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(BannerFormType::class, $banner, ['validation_groups' => $validation_group,]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $banner->setAuthor($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($banner);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_banner_edit', [
                        'id' => $banner->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:BannerAdmin:edit.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $banner = $this->get('customer_banner_manager')->findOneById($id);
        if ($banner instanceof Banner) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(Banner::PERMISSIONS_BANNER['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($banner->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->remove($banner);
                    $em->flush();

                    $response['status'] = true;
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_banner_index', [
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {

                    return new JsonResponse([
                        'status' => false,
                        'message' => $this->get('translator')->trans('banner.relationship_error')
                    ]);
                }
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }
}
