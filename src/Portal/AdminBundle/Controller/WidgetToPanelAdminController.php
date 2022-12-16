<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\WidgetToPanelType;
use Portal\ContentBundle\Entity\WidgetToPanel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class WidgetToPanelAdminController extends Controller
{
    public function createAction(Request $request, $instanceCode)
    {
        $widget = new WidgetToPanel();
        $form = $this->createForm(WidgetToPanelType::class, $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($widget);
                $em->flush();

                $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                return $this->redirectToRoute('admin_instance_widget_form_edit', [
                    'id' => $widget->getId(),
                    'instanceCode' => $instanceCode
                ]);
            }
        }

        return $this->render('PortalAdminBundle:WidgetToPanelAdmin:create.html.twig', [
            'form' => $form->createView(),
            'widget' => $widget,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $widget = $this->get('customer_widget_to_panel_manager')->find($id);
        $form = $this->createForm(WidgetToPanelType::class, $widget);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();

            if ($form->isValid() && $widget instanceof WidgetToPanel) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($widget);
                $em->flush();

                $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
            }
        }

        return $this->render('PortalAdminBundle:WidgetToPanelAdmin:edit.html.twig', [
            'widget' => $widget,
            'form' => $form->createView(),
            'instanceCode' => $instanceCode,
        ]);
    }

    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), WidgetToPanel::PERMISSIONS_WIDGET);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $widgetList = $this->get('customer_widget_to_panel_manager')->getAllWidgetToPanelForPagination();
        $adapter = new ArrayAdapter($widgetList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:WidgetToPanelAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }
    
    public function sortAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), WidgetToPanel::PERMISSIONS_WIDGET);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $widgetList = $this->get('customer_widget_to_panel_manager')->getAllWidgetToPanelForPagination();
        
        if ($request->isMethod('POST')) {
            $sortOrder = $request->request->get('sort_order');
            if (trim($sortOrder)) {
                try {
                    $widgetList = $this->get('customer_widget_to_panel_manager')->setWidgetToPanelOrder($sortOrder);
                } catch (\Doctrine\DBAL\DBALException $e) {
                    return new JsonResponse([
                        'status' => false, 
                        'message' => $this->get('translator')->trans('widget_to_panel_form.error_sorted'),
                            ]);
                }
                return new JsonResponse([
                    'status' => true, 
                    'message' => $this->get('translator')->trans('widget_to_panel_form.successfully_sorted'),
                        ]);
            }
            return new JsonResponse([
                'status' => false, 
                'message' => $this->get('translator')->trans('widget_to_panel_form.not_changes_for_sort'),
                    ]);
        }

        return $this->render('PortalAdminBundle:WidgetToPanelAdmin:sort.html.twig', [
            'instanceCode' => $instanceCode,
            'widgetList' => $widgetList,
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $resultResponse['redirectUrl'] = $this->get('router')->generate('admin_instance_widget_viewall', ['instanceCode' => $instanceCode]);

        $widget = $this->get('customer_widget_to_panel_manager')->find($id);
        if ($widget instanceof WidgetToPanel) {
            $em = $this->getDoctrine()->getManager('customer');
            $em->remove($widget);
            $em->flush();
            $resultResponse['status'] = true;
        } else {
            $resultResponse['status'] = false;
            $resultResponse['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($resultResponse);
    }
}
