<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\EventFormType;
use Portal\ContentBundle\Entity\Event;
use Doctrine\DBAL\DBALException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class EventAdminController extends Controller
{
    public function indexAction(Request $request, $instanceCode)
    {
        $filterParam = $request->query->all();
        $published = (isset($filterParam['filterPublished']) && $filterParam['filterPublished'] === 'true');
        $notPublished = (isset($filterParam['filterNotPublished']) && $filterParam['filterNotPublished'] === 'true');
        $param = [];
        if ($published xor $notPublished) {
            $param = ['isPublished' => $published ?: false];
        }

        $adapter = new ArrayAdapter($this->get('customer_event_manager')->findBy($param, ['id' => 'DESC', 'publishedAt' => 'DESC']));
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(Event::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:EventAdmin:index.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode,
            'filterParams' => $filterParam,
//            'users' => $this->getUsersArray()
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        if ($id == 0) {
            // create
            $event = new Event();
            $permissionCode = 'create_event';
            $isAuthor = false;
            $slug = false;
            $validation_group = ['new'];
        } else {
            // edit
            $event = $this->get('customer_event_manager')->findOneById($id);
            if (!$event instanceof Event || (
                    $event->getIsImportant() &&
                    $event->getOriginalInstanceCode() != $instanceCode &&
                    $event->getOriginalInstanceCode() != null
                )) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('error_page.text_404')
                );
            }
            $permissionCode = 'edit_event';
            $isAuthor = ($event->getAuthor() === $currentUserId);
            $slug = true;
            $validation_group = ['edit'];
        }
        $isGranted = $this->get('user_manager')->isGranted($permissionCode, $instanceCode, $currentUserId);
        if (!$isGranted && !$isAuthor) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(EventFormType::class, $event, [
            'slug' => $slug,
            'validation_groups' => $validation_group,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $event->setAuthor($currentUserId);
                    $event->setCreatedAt(date_create());
                    $event->setIsSearchIndexed(FALSE);
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($event);
                    $em->flush();
                    $id = $event->getId();

                    // make copy into main db
                    $eventMain = $this->get('event_manager')->findOneBy(['originalEventId' => $id]);
                    $emMain = $this->getDoctrine()->getManager();
                    $emMain->getConnection()->connect('master');
                    if ($event->getIsImportant()) {
                        if (!$eventMain instanceof Event) {
                            $eventMain = new Event();
                        }
                        $eventMain->setIsImportant($event->getIsImportant());
                        $eventMain->setAuthor($event->getAuthor());
                        $eventMain->setTitle($event->getTitle());
                        $eventMain->setSubtitle($event->getSubtitle());
                        $eventMain->setContent($event->getContent());
                        $eventMain->setStartDate($event->getStartDate());
                        $eventMain->setEndDate($event->getEndDate());
                        $eventMain->setIsPublished($event->getIsPublished());
                        $eventMain->setPublishedAt($event->getPublishedAt());
                        $eventMain->setOriginalInstanceCode($instanceCode);
                        $eventMain->setOriginalEventId($id);
                        $eventMain->setMenuNode(null);
                        $eventMain->setOriginalFilePath($event->getAttachment()->getPreviewFileUrl());
                        $emMain->persist($eventMain);
                        $emMain->flush();
                    } else {
                        // remove copy from main db
                        if ($eventMain instanceof Event) {
                            $emMain->remove($eventMain);
                            $emMain->flush();
                        }
                    }
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_event_edit', [
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

        return $this->render('PortalAdminBundle:EventAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'slug' => $slug,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $event = $this->get('customer_event_manager')->findOneById($id);
        if ($event instanceof Event) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isGranted = $this->get('user_manager')->isGranted('delete_event', $instanceCode, $currentUserId);
            $isAuthor = ($event->getAuthor() === $currentUserId);
            if ($isGranted || $isAuthor) {
                $em = $this->getDoctrine()->getManager('customer');
                $event->setIsSearchIndexed(FALSE);
                $em->remove($event);
                $em->flush();

                // remove copy from main db
                $event = $this->get('event_manager')->findOneBy(['originalEventId' => $id]);
                if ($event instanceof Event) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($event);
                    $em->flush();
                }

                $response['status'] = true;
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_event_index', [
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

//    protected function getUsersArray()
//    {
//        foreach ($this->get('user_manager')->getUsersInfo() as $v) {
//            $users[$v['id']] = $v['last_name'] . ' ' . $v['first_name'];
//        }
//        return $users ?? [];
//    }
}
