<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Slider;
use Portal\AdminBundle\Form\SliderType;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\DBALException;

class SliderAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Slider::PERMISSIONS_SLIDER);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $listSlider = $this->get('customer_slider_manager')->findAll();
        $adapter = new ArrayAdapter($listSlider);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:SliderAdmin:list.html.twig', [
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
            $slider = new Slider();
            $permissionCode = Slider::PERMISSIONS_SLIDER['create'];
            $isAuthor = false;
            $validation_group = ['new'];
        } else {
            // edit
            $slider = $this->get('customer_slider_manager')->findOneById($id);
            if (!$slider instanceof Slider) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = Slider::PERMISSIONS_SLIDER['edit'];
            $isAuthor = ($slider->getAuthor() === $currentUserId);
            $validation_group = ['edit'];
            foreach ($this->get('customer_slider_to_banner_manager')->getUsedBannerIds($id) as $v) {
                $banners[$v['title']] = $v['banner'];
            }
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(SliderType::class, $slider, ['validation_groups' => $validation_group]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $slider->setAuthor($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($slider);
                    $em->flush();

                    // update slider to banner data
                    $this->get('customer_slider_to_banner_manager')->setBannersToSlider(
                        $request->request->get('sort_order'),
                        $slider->getId()
                    );

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_slider_edit', [
                        'id' => $slider->getId(),
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:SliderAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'slider' => $slider,
            'instanceCode' => $instanceCode,
            'bannerList' => $this->get('customer_banner_manager')->findAll(),
            'banners' => $banners ?? []
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $slider = $this->get('customer_slider_manager')->findOneById($id);
        if ($slider instanceof Slider) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(Slider::PERMISSIONS_SLIDER['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($slider->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($slider);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_slider_index', [
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
