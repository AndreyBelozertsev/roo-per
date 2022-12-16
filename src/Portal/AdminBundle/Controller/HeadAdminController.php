<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\ContentBundle\Entity\Head;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\HeadFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class HeadAdminController extends Controller
{
    public function listAction(Request $request, $instanceCode)
    {
        $filterParam = $request->query->all();
        $headList = $this->get('customer_head_manager')->getAllHeadForPagination($filterParam);
        $adapter = new ArrayAdapter($headList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:HeadAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $filterParam,
            'users' => $this->getUsersArray()
        ]);
    }

    public function createAction(Request $request, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            'create_head',
            $instanceCode,
            $currentUserId
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $head = new Head();
        if ($request->query->get('menuNodeId')) {
            $head->setMenuNode($this->get('customer_menu_node_manager')->find($request->query->get('menuNodeId')));
        }

        $form = $this->createForm(HeadFormType::class, $head, [
            'validation_groups' => ['new']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $head->setAuthor($currentUserId);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($head);
                    $em->flush();
                    $id = $head->getId();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_head_form_edit', [
                        'id' => $id,
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:HeadAdmin:create.html.twig', [
            'form' => $form->createView(),
            'head' => $head,
            'slug' => false,
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $head = $this->get('customer_head_manager')->findOneById($id);
        if (!$head instanceof Head || $head->getIsDeleted()) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted('edit_head', $instanceCode, $currentUserId);
        $isAuthor = ($head->getAuthor() === $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $em = $this->getDoctrine()->getManager('customer');
        if ($head->getSlug() === '') {
            $head->setSlug($head->getFirstname() . '_' . $head->getMiddlename() . '_' . $head->getLastname() );
            $em->persist($head);
            $em->flush();
        }
        $form = $this->createForm(HeadFormType::class, $head, [
            'slug' => true,
            'validation_groups' => ['edit']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em->persist($head);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:HeadAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'head' => $head,
            'slug' => true,
            'instanceCode' => $instanceCode,
            'users' => $this->getUsersArray()
        ]);
    }

    public function deleteAction(Request $request, $id, $instanceCode)
    {
        $head = $this->get('customer_head_manager')->findOneById($id);
        if ($head instanceof Head) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted('delete_head', $instanceCode, $currentUserId);
            $isAuthor = ($head->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $head->setIsDeleted(true);
                $em->persist($head);
                $em->flush();

                if ($request->query->get('page') !== null) {
                    $response['redirectUrl'] = $this->get('router')->generate('admin_instance_head_list', [
                        'instanceCode' => $instanceCode,
                        'page' => (int)$request->query->get('page') ?: null
                    ]);
                } else {
                    $response['reload'] = true;
                }
                $response['status'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function restoreAction($id, $instanceCode)
    {
        $head = $this->get('customer_head_manager')->findOneById($id);
        if ($head instanceof Head) {
            $isGranted = $this->get('user_manager')->isGranted(
                'restore_head',
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager('customer');
                $head->setIsDeleted(false);
                $em->persist($head);
                $em->flush();

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_head_list', [
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

    /**
     * @return array
     */
    protected function getUsersArray()
    {
        $users = [];
        foreach ($this->get('user_manager')->getUsersInfo() as $v) {
            $users[$v['id']] = $v['last_name'] . ' ' . $v['first_name'];
        }
        return $users;
    }

    public function sortAction(Request $request)
    {
        try {
            $this->get('customer_head_manager')->setHeadSortOrder($request->get('ids'));
        } catch (DBALException $e) {
            $status = false;
            $message =  $this->get('translator')->trans('query_error');
        }

        return new JsonResponse([
            'status' => $status ?? true,
            'message' => $message ?? ''
        ]);
    }

    public function checkedOnIdAction($id, $instanceCode)
    {
        $response['status'] = false;
        $head = $this->get('customer_head_manager')->findOneBy(['id' => $id]);
        if ($head instanceof Head) {
            $isGranted = $this->get('user_manager')->isGranted(
                Head::PERMISSIONS_HEAD['edit'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $isLinked = $head->getIsLinkOnId() ? false : true;
                $head->setIsLinkOnId($isLinked);
                $em = $this->getDoctrine()->getManager('customer');
                $em->persist($head);
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
