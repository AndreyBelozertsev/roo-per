<?php

namespace Portal\ContentBundle\Controller;

use Portal\ContentBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\DBALException;
use Portal\HelperBundle\Helper\Pagination;

class DocumentController extends Controller
{
    public function viewAllAction(Request $request)
    {
        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $documentCount = $this->get('customer_document_manager')->getDocumentListCount();
        $totalPages = ceil($documentCount / Document::DOCUMENTS_LIMIT_ON_PAGE);
        $arrParam['pageCount'] = $totalPages;
        $currentPage = (int)$request->get('page') ?: 1;
        $currentPage = ($currentPage < $totalPages) ? $currentPage : $totalPages;
        $arrParam['currentPage'] = $currentPage;
        $arrParam['hideButton'] = ($currentPage >= $totalPages);

        $arrParam['documents'] = $this->get('customer_document_manager')->getMoreDocumentList($currentPage - 1);

        $pagination = new Pagination($this->container);
        $arrParam['pagination'] = $pagination->render($currentPage, $totalPages);
//        $lastDate = new \DateTime();
//        $arrParam['lastTime'] = $lastDate->format('d-m-Y H:i:s');

        return $this->render('PortalContentBundle:Document:documents.html.twig', $arrParam);
    }

    public function showAction(Request $request)
    {
        if ($slug = $request->get('slug')) {
            $params['slug'] = $slug;
        } else {
            $params['id'] = $request->get('id');
        }
        $params['documentType'] = Document::TYPE_PUBLIC_DOCUMENT;
        $params['isPublished'] = true;
        $params['isDeleted'] = false;

        $document = $this->get('customer_document_manager')->findOneBy($params);
        if (!$document instanceof Document) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        $id = $document->getId();

        $arrParam['isDepartment'] = false;
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParam['isDepartment'] = true;
            $arrParam['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);
        }
        $arrParam['document'] = $document;
        $arrParam['attachments'] = $this->get('customer_document_attachment_manager')->getAttachmentListByDocumentId($id);
        $arrParam['tags'] = $this->get('customer_tag_manager')->getTagsByDocumentId($id);

        return $this->render('PortalContentBundle:Document:show.html.twig', $arrParam);
    }

    public function getMoreDocumentAction(Request $request)
    {
        try {
            $currentPage = $request->get('page');
//            $lastTime = $request->get('lasttime');

            $arrJson['content'] = $this->render('PortalContentBundle:Document:docItem.html.twig', [
                'documents' => $this->get('customer_document_manager')->getMoreDocumentList($currentPage),
                'site_name' => $this->getParameter('site_name')
            ])->getContent();

            $documentCount = $this->get('customer_document_manager')->getDocumentListCount();
            $totalPages = ceil($documentCount / Document::DOCUMENTS_LIMIT_ON_PAGE);
            $pagination = new Pagination($this->container);
            $arrJson['pagination'] = $pagination->render(
                $currentPage + 1,
                $totalPages,
                'view_all_documents'
            );
            $arrJson['status'] = true;
        } catch(DBALException $e) {

            $arrJson['message'] = $this->get('translator')->trans('wrong_data');
        }

        return new JsonResponse($arrJson);
    }
}
