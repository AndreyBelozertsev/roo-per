<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\PageFormType;
use Portal\ContentBundle\Entity\Page;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

use Portal\AdminBundle\Form\StandardTemplateFormType;
use Portal\AdminBundle\Form\InteractiveMapTemplateFormType;
use Portal\AdminBundle\Form\VisitcardTemplateFormType;
use Portal\AdminBundle\Form\TableTemplateFormType;
use Portal\AdminBundle\Form\ContactTemplateFormType;
use Portal\AdminBundle\Form\SiteMapTemplateFormType;
use Portal\ContentBundle\Entity\StandardTemplate;
use Portal\ContentBundle\Entity\InteractiveMapTemplate;
use Portal\ContentBundle\Entity\VisitcardTemplate;
use Portal\ContentBundle\Entity\TableTemplate;
use Portal\ContentBundle\Entity\ContactTemplate;
use Portal\ContentBundle\Entity\SiteMapTemplate;

class InformPageAdminController extends Controller
{
    private $_formType = [
        1 => StandardTemplateFormType::class,
        2 => InteractiveMapTemplateFormType::class,
        3 => VisitcardTemplateFormType::class,
        4 => TableTemplateFormType::class,
        5 => ContactTemplateFormType::class,
        6 => SiteMapTemplateFormType::class
    ];
    private $_templateClassName = [
        1 => StandardTemplate::class,
        2 => InteractiveMapTemplate::class,
        3 => VisitcardTemplate::class,
        4 => TableTemplate::class,
        5 => ContactTemplate::class,
        6 => SiteMapTemplate::class
    ];

    public function listAction(Request $request, $instanceCode)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Page::PERMISSIONS_INFORM_PAGE);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $adapter = new ArrayAdapter($this->get('customer_page_manager')->getAllPages());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(Page::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:InformPage:index.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            Page::PERMISSIONS_INFORM_PAGE['create'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $informPage = new Page();
        $form = $this->createForm(PageFormType::class, $informPage, [
            'validation_groups' => ['new']
        ]);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {

            return $this->render('PortalAdminBundle:InformPage:create.html.twig', [
                'form' => $form->createView(),
                'instanceCode' => $instanceCode
            ]);
        } elseif ($form->isValid()) {
            $informPage->setAuthorId($this->get('user_helper')->getCurrentUser()->getId());
            $informPage->setCreatedAt(date_create());
            $em = $this->getDoctrine()->getManager('customer');
            $em->persist($informPage);
            $em->flush();
            $tplId = $informPage->getTemplateId();
            $template = new $this->_templateClassName[$tplId];
            $template->setPageId($informPage);
            $templateForm = $this->createForm($this->_formType[$tplId], $template);

            return new JsonResponse([
                'status' => true,
                'content' => $this->render('PortalAdminBundle:InformPage:createTemplate.html.twig', [
                    'form' => $templateForm->createView(),
                    'templateId' => $tplId,
                    'instanceCode' => $instanceCode
                ])->getContent()
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'content' => $this->render('PortalAdminBundle:InformPage:createForm.html.twig', [
                'form' => $form->createView(),
                'instanceCode' => $instanceCode,
            ])->getContent()
        ]);
    }

    public function templateCreateAction(Request $request, $instanceCode)
    {
        $tplId = (int)$request->request->get('templateId');
        $template = new $this->_templateClassName[$tplId];
        $form = $this->createForm($this->_formType[$tplId], $template, [
            'validation_groups' => ['new']
        ]);
        $form->handleRequest($request);

        $flashBag = $this->get('session')->getFlashBag();
        if (!$form->isSubmitted()) {

            return new JsonResponse([
                'content' => $this->render('PortalAdminBundle:InformPage:createTemplateForm.html.twig', [
                    'form' => $form->createView(),
                    'templateId' => $tplId,
                    'instanceCode' => $instanceCode,
                ])->getContent()
            ]);
        } elseif ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('customer');
            $em->persist($template);
            $em->flush();
            $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

            return $this->redirectToRoute('admin_instance_inform_page_list', ['instanceCode' => $instanceCode]);
        }
        $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));

        return $this->redirectToRoute('admin_instance_inform_page_edit', [
            'id' => $form->getData()->getPageId()->getId(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $page = $this->get('customer_page_manager')->find($id);
        if (!$page instanceof Page) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        } else {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isAuthor = ($page->getAuthorId() === $currentUserId);
            $isGranted = $this->get('user_manager')->isGranted(Page::PERMISSIONS_INFORM_PAGE['edit'], $instanceCode, $currentUserId);
            if (!$isGranted && !$isAuthor) {
                throw $this->createAccessDeniedException(
                    $this->get('translator')->trans('error_page.text_403')
                );
            }

            $tplId = $page->getTemplateId();
            $template = $this->get('customer_page_manager')->findTemplate($tplId, $id);
            if (!$template instanceof $this->_templateClassName[$tplId]) {
                $template = new $this->_templateClassName[$tplId];
            }
            $form = $this->createForm($this->_formType[$tplId], $template, [
                'validation_groups' => ['edit']
            ]);
            $form->add('pageId', HiddenType::class);

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $flashBag = $this->get('session')->getFlashBag();
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->getConnection()->beginTransaction();
                    try {
                        $template->setPageId($page);
                        $em->persist($template);

                        $page->setTitle($request->request->get('pageTitle'));
                        $page->setSlug($request->request->get('pageSlug'));
                        $page->setUpdatedAt(date_create());
                        $em->persist($page);

                        $em->flush();
                        $em->getConnection()->commit();
                        $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                        return $this->redirectToRoute('admin_instance_inform_page_edit', [
                            'id' => $id,
                            'instanceCode' => $instanceCode
                        ]);

                    } catch (\Exception $e) {
                        $em->getConnection()->rollback();
                        $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                    }
                } else {
                    $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
                }
            }
        }

        return $this->render('PortalAdminBundle:InformPage:edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
            'template' => $template,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $page = $this->get('customer_page_manager')->find($id);
        if ($page instanceof Page) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted(Page::PERMISSIONS_INFORM_PAGE['delete'], $instanceCode, $currentUserId);
            $isAuthor = ($page->getAuthorId() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $template = $this->get('customer_page_manager')->findTemplate($page->getTemplateId(), $id);
                if ($template !== null) {
                    $em->remove($template);
                }
                $em->remove($page);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_inform_page_list', [
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
