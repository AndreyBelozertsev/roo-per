<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\MenuFormType;
use Portal\ContentBundle\Entity\Menu;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Doctrine\DBAL\DBALException;

class MenuAdminController extends Controller
{
    const MAX_PER_PAGE_PAGINATION = 10;

    public function viewAllAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Menu::PERMISSIONS_MENU);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $adapter = new ArrayAdapter($this->get('customer_menu_manager')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:MenuAdmin:viewAll.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $menuId, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($menuId == 0) {
            // create
            $menu = new Menu();
            $permissionCode = Menu::PERMISSIONS_MENU['create'];
            $isAuthor = false;
        } else {
            // edit
            $menu = $this->get('customer_menu_manager')->findOneById($menuId);
            if (!$menu instanceof Menu) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = Menu::PERMISSIONS_MENU['edit'];
            $isAuthor = ($menu->getAuthor() === $currentUserId);
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $slug = PortalHelper::slug($menu->getTitle());
                    $menu->setCode($slug);
                    $menu->setAuthor($currentUserId);
                    $menu->setCreatedAt(date_create());
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($menu);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_menu_viewall', ['instanceCode' => $instanceCode]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:MenuAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($menuId, $instanceCode)
    {
        $menu = $this->get('customer_menu_manager')->findOneById($menuId);
        if ($menu instanceof Menu) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(Menu::PERMISSIONS_MENU['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($menu->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($menu);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_menu_viewall', [
                    'instanceCode' => $instanceCode
                ]);
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('menu_page.already_deleted');
        }

        return new JsonResponse($response);
    }
}
