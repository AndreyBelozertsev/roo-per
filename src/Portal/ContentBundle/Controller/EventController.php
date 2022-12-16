<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Portal\ContentBundle\Entity\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;
use Portal\HelperBundle\Helper\Pagination;

class EventController extends Controller
{
    public function indexAction(Request $request)
    {
        $arrParams['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        $date = $this->getPeriod($request->get('date'));
        $arrParams['paramDate'] = $date['original'];

        $eventCount = $this->get('customer_event_manager')->getActualEventCount($date['start'], $date['end']);
        $totalPages = ceil($eventCount / Event::LIMIT_EVENTS_DEFAULT) ?: 1;
        $arrParams['pageCount'] = $totalPages;

        $currentPage = (int)$request->get('page') ?: 1;
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $arrParams['currentPage'] = $currentPage;
        $arrParams['hideButton'] = ($currentPage >= $totalPages);
        $pagination = new Pagination($this->container);
        $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);

        $arrParams['eventList'] = $this->get('customer_event_manager')->getActualEventList(
            $date['start'],
            $date['end'],
            $currentPage - 1,
            Event::LIMIT_EVENTS_DEFAULT
        );

        return $this->render('PortalContentBundle:Event:index.html.twig', $arrParams);
    }

    public function showAction(Request $request)
    {
        $arrParams['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        if ($request->get('slug')) {
            $param['slug'] = $request->get('slug');
        } else {
            $param['id'] = (int)$request->get('id');
        }
        $event = $this->get('customer_event_manager')->findOneBy($param);
        if (!$event instanceof Event || !$event->getIsPublished()) {
            $errorMessage = $this->get('translator')->trans('no_event');
            throw $this->createNotFoundException($errorMessage);
        }
        $id = $event->getId();
        $arrParams['event'] = $event;
        $arrParams['tags'] = $this->get('customer_tag_manager')->getTagsByEventId($id);

        if ($event->getPhotoReport() !== null) {
            $arrParams['photoReport'] = $this->get('customer_photo_report_manager')
                ->getAttachmentListPhotoReportById($event->getPhotoReport()->getId());
        }

        return $this->render('PortalContentBundle:Event:show.html.twig', $arrParams);
    }

    // ajax request on click "get_more" button
    public function getMoreEventAction(Request $request)
    {
        $date = $this->getPeriod($request->get('date'));
        try {
            $arrParams['status'] = true;

            $eventCount = $this->get('customer_event_manager')->getActualEventCount($date['start'], $date['end']);
            $totalPages = ceil($eventCount / Event::LIMIT_EVENTS_DEFAULT);
            $currentPage = (int)$request->get('page');
            $pagination = new Pagination($this->container);
            $arrParams['pagination'] = $pagination->render(
                $currentPage + 1,
                $totalPages,
                'event'
            );

            $arrParams['content'] = $this->render('PortalContentBundle:Event:eventItems.html.twig', [
                'eventList' => $this->get('customer_event_manager')->getActualEventList(
                    $date['start'],
                    $date['end'],
                    $currentPage,
                    Event::LIMIT_EVENTS_DEFAULT
                )
            ])->getContent();
        } catch (DBALException $e) {
            $arrParams['message'] = $this->get('translator')->trans('wrong_data');
        }

        return new JsonResponse($arrParams);
    }

    public function eventListByDateAction(Request $request)
    {
        $date = $request->request->get('date');
        $arrParams = [
            'message' => '',
            'status' => false,
            'content' => ''
        ];
        try {
            $arrParams['content'] = $this->render('PortalContentBundle:Event:eventScroller.html.twig', [
                'eventList' => $this->get('customer_event_manager')->getEventListByDate($date)
            ])->getContent();
            $arrParams['status'] = true;
        } catch (DBALException $e) {
            $arrParams['message'] = $this->get('translator')->trans('wrong_date');
        }

        return new JsonResponse($arrParams);
    }

    public function eventListDayOnMonthAction(Request $request)
    {
        $date = $request->request->get('date');
        $arrParams = [
            'message' => '',
            'status' => false,
            'content' => ''
        ];
        try {
            $arrParams['content'] = $this->get('event_manager')->getEventListDayOnMonth($date);
            $arrParams['status'] = true;
        } catch (DBALException $e) {
            $arrParams['message'] = $this->get('translator')->trans('wrong_date');
        }

        return new JsonResponse($arrParams);
    }

    public function getNextEventsAction(Request $request)
    {
        return new JsonResponse([
            'status' => true,
            'content' => $this->get('event')->visionRender($request->get('page'))
        ]);
    }

    /**
     * @param $date
     *
     * @return array|bool
     */
    protected function getPeriod($date)
    {
        preg_match('/(\d{1,2}).(\d{4})/', $date, $matches);
        if (count($matches) !== 0) {
            $m = (int)$matches[1];
            $y = (int)$matches[2];

            if ($m) {
                // 1 month period
                $startDate = date('01.m.Y', strtotime('01.' . $m . '.' . $y) ?: time());
                $endDate = date('d.m.Y', strtotime($startDate . '+1 month'));
            } else {
                // 1 year period
                $startDate = date('01.m.Y', strtotime('01.01.' . $y) ?: time());
                $endDate = date('d.m.Y', strtotime('01.01.' . ($y + 1) ));
                $original = '01.0.' . $y;
            }

            return [
                'start' => $startDate,
                'end' => $endDate,
                'original' => $original ?? $startDate
            ];
        }

        return false;
    }
}
