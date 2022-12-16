<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\Menu;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Portal\AdminBundle\Form\MenuNodeFormType;
use Portal\ContentBundle\Entity\MenuNode;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class MenuNodeAdminController extends Controller
{
    const MAX_PER_PAGE_PAGINATION = 10;

    public function viewAllAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), MenuNode::PERMISSIONS_MENU_NODE);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $filterParam = $request->query->all();
        $menuNodeList = $this->get('customer_menu_node_manager')->getAllMenuNodeForPagination($filterParam);
        $adapter = new ArrayAdapter($menuNodeList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('PortalAdminBundle:MenuNodeAdmin:viewAll.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $filterParam
        ]);
    }

    public function editAction(Request $request, $menuNodeId, $instanceCode)
    {
        if ($menuNodeId == 0) {
            // create
            $menuNode = new MenuNode();
            $slug = false;
//            $validation_groups = [];
            $permissionCode = MenuNode::PERMISSIONS_MENU_NODE['create'];
            $structureId = $request->get('structureId');
            if ($structureId) {
                $permissionCode = MenuNode::PERMISSIONS_STRUCTURE['create'];
                $parentStructure = $this->get('customer_menu_node_manager')->find($structureId);
                $menuNode->setParent($parentStructure);
                $menuNode->setMenu($parentStructure->getMenu());
            }
        } else {
            // edit
            $menuNode = $this->get('customer_menu_node_manager')->findOneBy(['id' => $menuNodeId]);
            if (!$menuNode instanceof MenuNode || $menuNode->getIsDeleted()) {

                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $slug = true;
//            $validation_groups = ['edit'];
            $permissionCode = MenuNode::PERMISSIONS_MENU_NODE['edit'];
            if ($menuNode->getMenu()->getCode() === Menu::STRUCTURE_MENU) {
                $permissionCode = MenuNode::PERMISSIONS_STRUCTURE['edit'];
            }
            if ($menuNode->getSlug() === null) {
                $menuNode->setSlug($menuNode->getTitle());
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($menuNode);
                $em->flush();
            }
        }
        $isGranted = $this->get('user_manager')->isGranted(
            $permissionCode,
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {

            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(MenuNodeFormType::class, $menuNode, [
            'slug' => $slug,
            'isSuperAdmin' => $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
//            'validation_groups' => $validation_groups,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');

                    if ($menuNode->getMenu()->getCode() === Menu::STRUCTURE_MENU) {
                        $parentId = ($menuNode->getParent() === null) ? null : $menuNode->getParent()->getId();
                        $this->get('customer_menu_node_manager')->resortingStructure($parentId);
                    }
                    $menuNode->setIsSearchIndexed(FALSE);
                    $em->persist($menuNode);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_menu_node_edit', [
                        'instanceCode' => $instanceCode,
                        'menuNodeId' => $menuNode->getId()
                    ]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:MenuNodeAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode,
            'slug' => $slug,
            'menuNode' => $menuNode,
        ]);
    }

    public function deleteAction($menuNodeId, $instanceCode)
    {
        $menuNode = $this->get('customer_menu_node_manager')->findOneBy(['id' => $menuNodeId]);
        if ($menuNode instanceof MenuNode) {
            $isGranted = $this->get('user_manager')->isGranted(
                MenuNode::PERMISSIONS_MENU_NODE['delete'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $menuNode->setIsSearchIndexed(FALSE);
                $em->remove($menuNode);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_menu_node_viewall', [
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

    public function checkedOnIdAction($menuNodeId, $instanceCode)
    {
        $response['status'] = false;
        $menuNode = $this->get('customer_menu_node_manager')->findOneBy(['id' => $menuNodeId]);
        if ($menuNode instanceof MenuNode) {
            $isGranted = $this->get('user_manager')->isGranted(
                MenuNode::PERMISSIONS_MENU_NODE['edit'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $isLinked = $menuNode->getIsLinkOnId() ? false : true;
                $menuNode->setIsLinkOnId($isLinked);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($menuNode);
                $em->flush();
                $response['status'] = true;
                $response['message'] = $this->get('translator')->trans('successfully_save');
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('wrong_data');
        }

        return new JsonResponse($response);
    }
}
