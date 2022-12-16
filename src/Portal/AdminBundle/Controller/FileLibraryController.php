<?php

namespace Portal\AdminBundle\Controller;

use Portal\HelperBundle\Libs\Pagination;
use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FileLibraryController extends Controller
{
    public function listFilesAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            Attachment::PERMISSIONS_FILE_LIB['read'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $extensionArray = [];
        $extensionArray = array_merge($extensionArray, Attachment::AUDIO, Attachment::VIDEO, Attachment::IMAGE);
        $typeArray = [
            'image' => Attachment::IMAGE,
            'video' => Attachment::VIDEO,
            'audio' => Attachment::AUDIO
        ];
//      get value for filter by mime type and extension
        $extension = $request->query->get('filterExtension');
        $type = $request->query->get('filterType');

        $searchParams['image'] = $request->get('image');
        $searchParams['sort'] = $request->get('filterSort');
//      search extension in array
        if (!empty($extension)) {
            $searchParams['extension'] = implode(',', array_filter($extensionArray, function ($v) use ($extension) {
                    if ($extension) {
                        return $v == $extension;
                    }
                    return true;
                })
            );
        }
        $extensionType = array_keys($typeArray);
//      search type in array
        if (!empty($type)) {
            $typeArray = array_filter($typeArray, function ($v, $k) use ($type) {
                if ($type) {
                    return $k == $type;
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);

            if (key_exists($type, $typeArray)) {
                $searchParams['type'] = "'" . implode("','", $typeArray[$type]) . "'";
            }
        }
//      current page default 0
        $page = (int)$request->query->get('page');
        $em = $this->get('customer_attachment_manager');
        $countFiles = $em->getCountFiles($searchParams);
        $pagination = new Pagination($countFiles, Attachment::LIMIT_FILES, $page);
//      Pagination parameters
        $searchParams['limit'] = $pagination->getLimit();
        $searchParams['offset'] = $pagination->getOffset();

        return $this->render('PortalAdminBundle:FileLibrary:list.html.twig', [
            'instanceCode' => $instanceCode,
            'files' => $em->getAllAttachments($searchParams),
            'pagination' => $pagination,
            'extension' => $extensionArray,
            'extension_type' => $extensionType,
            'query' => $request->query->all()
        ]);
    }

    public function listDocumetnFilesAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            Attachment::PERMISSIONS_DOCUMENT_LIB['read'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $extensionArray = [];
        $extensionArray = array_merge($extensionArray, Attachment::DOCUMENTS);
        $typeArray = ['documents' => Attachment::DOCUMENTS];
//      get value for filter by mime type and extension
        $extension = $request->query->get('filterExtension');
        $type = $request->query->get('filterType');

        $searchParams['image'] = $request->get('image');
        $searchParams['sort'] = $request->get('filterSort');
//      search extension in array
        if (!empty($extension)) {
            $searchParams['extension'] = implode(',', array_filter($extensionArray, function ($v) use ($extension) {
                    if ($extension) {
                        return $v == $extension;
                    }
                    return true;
                })
            );
        }
        $extensionType = array_keys($typeArray);
//      search type in array
        if (!empty($type)) {
            $typeArray = array_filter($typeArray, function ($v, $k) use ($type) {
                if ($type) {
                    return $k == $type;
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);

            if (key_exists($type, $typeArray)) {
                $searchParams['type'] = "'" . implode("','", $typeArray[$type]) . "'";
            }
        }
//      current page default 0
        $page = (int)$request->query->get('page');
        $em = $this->get('customer_attachment_manager');
        $countFiles = $em->getCountDocumentFiles($searchParams);
        $pagination = new Pagination($countFiles, Attachment::LIMIT_FILES, $page);
//      Pagination parameters
        $searchParams['limit'] = $pagination->getLimit();
        $searchParams['offset'] = $pagination->getOffset();

        return $this->render('PortalAdminBundle:FileLibrary:listDocumentAttachment.html.twig', [
            'instanceCode' => $instanceCode,
            'files' => $em->getDocumetnAttachments($searchParams),
            'pagination' => $pagination,
            'extension' => $extensionArray,
            'extension_type' => $extensionType,
            'query' => $request->query->all()
        ]);
    }

    public function deleteAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            Attachment::PERMISSIONS_FILE_LIB['delete'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if ($isGranted) {
            try {
                $attachmentIds = $request->get('ids');
                $this->get('customer_attachment_manager')->deleteFilesByIds($attachmentIds);
                $response['status'] = true;
            } catch (\Exception $e) {
                $response['status'] = false;
                $response['message'] = $this->get('translator')->trans('delete_error');

                return new JsonResponse($response);
            }
            $response['redirectUrl'] = $this->get('router')->generate($request->get('route'), ['instanceCode' => $instanceCode]);
        } else {
            $response['message'] = $this->get('translator')->trans('error_page.text_403');
        }

        return new JsonResponse($response);
    }
}
