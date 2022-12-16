<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\SystemModeType;
use Portal\ContentBundle\Entity\SystemMode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class SystemModeAdminController extends Controller
{
    public function editAction(Request $request, $id)
    {
        $options = [];
        $systemMode = $this->get('system_mode_manager')->find($id);
        $form = $this->createForm(SystemModeType::class, $systemMode, $options);

        $form->handleRequest($request);
        if ($form->isValid() && $systemMode instanceof SystemMode) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($systemMode);
            $em->flush();
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

            return $this->redirectToRoute('admin_admin_system_mode_edit', [
                'id' => $id,
            ]);
        }

        return $this->render('PortalAdminBundle:SystemModeAdmin:edit.html.twig', [
            'article' => $systemMode,
            'form' => $form->createView(),
        ]);
    }

    public function viewAllAction(Request $request)
    {
        $SystemModeList = $this->get('system_mode_manager')->getAllSystemMode();
        $adapter = new ArrayAdapter($SystemModeList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:SystemModeAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
        ]);
    }
}
