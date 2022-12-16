<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\Document;
use Portal\ContentBundle\Entity\Event;
use Portal\ContentBundle\Entity\FeedbackForm;
use Portal\ContentBundle\Entity\Material;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\MenuNode;
use Portal\ContentBundle\Entity\Page;
use Portal\ContentBundle\Entity\Quiz;
use Portal\ContentBundle\Entity\StructureTemplate;
use Portal\HelperBundle\Helper\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StructureContentController extends Controller
{
    public function indexAction(Request $request)
    {
        $arrParams['isDepartment'] = false;

        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            // department page
            $arrParams['isDepartment'] = true;
//            $arrParams['depCode'] = $instanceCode;
            $arrParams['instanceCode'] = $instanceCode;
            $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        if ($request->get('slug')) {
            $param['slug'] = $request->get('slug');
            $slugPath = true;
        } else {
            $param['id'] = (int)$request->get('id');
            $slugPath = false;
        }

        $em = $this->getDoctrine()->getManager('customer');
        $block = $em->getRepository(MenuNode::class)->findOneBy($param);
//        VarDumper::dump($this->get('customer_menu_node_manager')->getCountRemovedParents($block->getId())); die();
        if ($block instanceof MenuNode && $block->getIsPublished() && !$block->getIsDeleted() &&
            $this->get('customer_menu_node_manager')->getCountRemovedParents($block->getId()) == 0) {
            $blockId = $block->getId();

            if ($block->getIsLinkOnId() && $slugPath) {
                return $this->redirectToRoute('structure', ['id' => $blockId], 301);
            }
            if (!$block->getIsLinkOnId() && !$slugPath && trim($block->getSlug()) !== '') {
                return $this->redirectToRoute('structure_slug', ['slug' => $block->getSlug()], 301);
            }

            if ($block->getStructureTemplate() instanceof StructureTemplate) {
                $structureTemplate = $block->getStructureTemplate()->getCode();
            } else {
                $structureTemplate = 'simple';
                $items['photo_reports'] = $block->getPhotoReports();
                $items['video_reports'] = $block->getVideoReports();
            }

            // We can hide childs
            $blockChilds = [];
            if (!$block->getIsHideChilds()) {
                $blockChilds = $this->get('customer_menu_node_manager')->findBy(
                    ['parent' => $blockId, 'isPublished' => true, 'isHidden' => false, 'isDeleted' => false],
                    ['order' => 'ASC', 'title' => 'ASC']
                );
            }
            $items['childs'] = $blockChilds;
            $arrParams['isStructure'] = ($block->getMenu() !== null && $block->getMenu()->getCode() === Menu::STRUCTURE_MENU);

            $documentCount = $this->get('customer_document_manager')->getPublishedDocumentCount($blockId);
            $totalPages = ceil($documentCount / Document::DOCUMENTS_LIMIT_ON_PAGE);
            $arrParams['pageCount'] = $totalPages;
            $currentPage = (int)$request->get('page') ?: 1;
            $items['documents'] = $this->get('customer_document_manager')->getPublishedDocumentList($blockId, $currentPage - 1);
            $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;

            $pagination = new Pagination($this->container);
            $arrParams['pagination'] = $pagination->render($currentPage, $totalPages);

            if ($structureTemplate === 'photo_report') {
                $items['photo_reports'] = $this->get('customer_photo_report_manager')->getStructurePhotoReportList($blockId);
                $arrParams['photoReportCount'] = $this->get('customer_photo_report_manager')->getStructurePhotoReportListCount($blockId);
            } elseif ($structureTemplate === 'video_report') {
                $items['video_reports'] = $this->get('customer_video_report_manager')->getStructureVideoReportList($blockId);
                $arrParams['videoReportCount'] = $this->get('customer_video_report_manager')->getStructureVideoReportListCount($blockId);
            } elseif ($structureTemplate === 'article') {
//                $structureTemplate = 'article_new';
                $items['articles'] = $this->get('customer_article_manager')->getArticleList();
                ////!!!!!!KOSTIL!!!!!!!!!!!!
                $ignoreTime = date('Y-m-d H:i:s');
                $arrParams['ignoreTime'] = $ignoreTime;
                $arrParams['article_count'] = $this->get('customer_article_manager')->getCountArticleUntil($ignoreTime);
//            } elseif ($structureTemplate === 'document') {
//                $structureTemplate = 'document_new';
            } elseif ($structureTemplate === 'head') {
                $items['heads'] = $this->get('customer_head_manager')->getStructureHeadList($blockId);
            } else {
//                $structureTemplate = 'article_new';
//                $items['articles'] = $this->get('customer_article_manager')->getArticleList();
//                $items['articles'] = $this->get('customer_structure_template_manager')->getStructureArticlesList($blockId);
                $items['pages'] = $em->getRepository(Page::class)->findBy(['isPublished' => 'true', 'menuNode' => $blockId]);
                $items['quizzes'] = $em->getRepository(Quiz::class)->findBy(['isPublished' => 'true', 'menuNode' => $blockId]);
                $items['events'] = $em->getRepository(Event::class)->findBy(['isPublished' => 'true', 'menuNode' => $blockId]);
                $items['feedbacks'] = $em->getRepository(FeedbackForm::class)->findBy(['isPublished' => 'true', 'menuNode' => $blockId]);
                $items['interviews'] = $this->get('customer_structure_template_manager')->getStructureInterviewsList($blockId);
                $items['materials'] = $em->getRepository(Material::class)->findBy(['isPublished' => 'true', 'menuNode' => $blockId]);
            }
            $arrParams['itemsStructure'] = $items;
            $arrParams['title'] = $block->getTitle();
            $arrParams['beforeText'] = $block->getBeforeText();
            $arrParams['afterText'] = $block->getAfterText();
            $arrParams['isSeparator'] = $block->getIsSeparator();
            $arrParams['isHidden'] = $block->getIsHidden();
            $arrParams['structureTemplate'] = $structureTemplate;
            $arrParams['structureId'] = $blockId;
            $arrParams['createdAt'] = $block->getManualCreatedAt() ?? $block->getCreatedAt();
            $arrParams['updatedAt'] = $block->getManualUpdatedAt() ?? ($block->getUpdatedAt() ?: $block->getCreatedAt());
        } else {
            $errorMessage = $this->get('translator')->trans('no_structure');
            throw $this->createNotFoundException($errorMessage);
        }

        switch ($structureTemplate) {
            case 'photo_report':
                return $this->render('PortalContentBundle:StructureTemplate:template_photo_report.html.twig', $arrParams);

            case 'video_report':
                return $this->render('PortalContentBundle:StructureTemplate:template_video_report.html.twig', $arrParams);

            default:
//                $arrParams['structureTemplate'] = $structureTemplate;
                return $this->render('PortalContentBundle:StructureTemplate:base.structure.template.html.twig', $arrParams);
        }
    }

    public function getNextStructurePhotoReportAction(Request $request)
    {
        $photoReportList = $this->get('customer_photo_report_manager')->getStructurePhotoReportList(
            (int)$request->get('structureId'),
            (int)$request->get('shown')
        );
        if (false !== $photoReportList) {
            $arrJson['listCount'] = sizeof($photoReportList);
            $arrParams['photoReportList'] = $photoReportList;
        } else {
            $arrParams['message'] = $this->get('translator')->trans('no_records');
        }
        $arrJson['content'] = $this->render(
            'PortalContentBundle:PhotoReport:show_next_photo.html.twig',
            $arrParams ?? []
        )->getContent();

        return new JsonResponse($arrJson);
    }

    public function getNextStructureVideoReportAction(Request $request)
    {
        $videoReportList = $this->get('customer_video_report_manager')->getStructureVideoReportList(
            (int)$request->get('structureId'),
            (int)$request->get('shown')
        );
        if (false !== $videoReportList) {
            $arrJson['listCount'] = sizeof($videoReportList);
            $arrParams['videoReportList'] = $videoReportList;
        } else {
            $arrParams['message'] = $this->get('translator')->trans('no_records');
        }
        $arrJson['content'] = $this->render(
            'PortalContentBundle:VideoReport:show_next_video.html.twig',
            $arrParams ?? []
        )->getContent();

        return new JsonResponse($arrJson);
    }
}
