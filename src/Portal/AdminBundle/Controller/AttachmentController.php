<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AttachmentController extends Controller
{
    public function uploadAction(Request $request)
    {
//        $uploadedFile = $request->files->get('file');
//        if ($uploadedFile === null) {
//            return new JsonResponse([
//                'status' => false,
//                'message' => $this->get('translator')->trans('file_not_select')
//            ]);
//        }
//
//        $finfo = finfo_open(FILEINFO_MIME_TYPE);
//        $fileType = finfo_file($finfo, $uploadedFile);
//        finfo_close($finfo);
//        if (!in_array($fileType, Attachment::$ALLOWED_FILE_TYPES)) {
//            return new JsonResponse([
//                'status' => false,
//                'message' => $this->get('translator')->trans('not_allowed_file_type')
//            ]);
//        }
//
//        $fileSize = $uploadedFile->getSize();
//        if ($fileSize > Attachment::MAX_FILE_SIZE) {
//            return new JsonResponse([
//                'status' => false,
//                'message' => $this->get('translator')->trans('to_big_file')
//            ]);
//        }
//
//        $pathListKey = $request->request->get('dir_code');
//        if (!isset(Attachment::$PATH_LIST[$pathListKey])) {
//            return new JsonResponse([
//                'status' => false,
//                'message' => $this->get('translator')->trans('no_dir_code')
//            ]);
//        }
//
//        $uploadedDir = '/' . Attachment::FILE_DIR
//            . $request->request->get('instance_code') . '/'
//            . Attachment::ATTACHMENTS_DIR
//            . Attachment::$PATH_LIST[$pathListKey]
//        ;
//        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $uploadedDir;
//        if (!is_dir($fullPath)) {
//            return new JsonResponse([
//                'status' => false,
//                'message' => $this->get('translator')->trans('no_path')
//            ]);
//        }
//
//        $label = $request->request->get('label');
//        $originalFileName = $uploadedFile->getClientOriginalName();
//        $uploadedFile->move($fullPath, $originalFileName);
//
//        $attachment = new Attachment();
//        $attachment->setLabel($label);
//        $attachment->setType($pathListKey);
//        $attachment->setFilename($originalFileName);
//        $attachment->setOriginalName($originalFileName);
//        $attachment->setPath($uploadedDir);
//        $attachment->setFileType(Attachment::DEFAULT_FILE_TYPE);
//        $attachment->setDescription(null);
//
//        $em = $this->getDoctrine()->getManager('customer');
//        $em->persist($attachment);
//        $em->flush();
//
//        return new JsonResponse([
//            'status' => true,
//            'id' => $attachment->getId(),
//            'fileName' => $originalFileName,
//            'uploadedDir' => $uploadedDir,
//            'label' => $label
//        ]);

        return new JsonResponse([]);
    }

    public function getUploaderAction()
    {
        $arrParams['instances'] = $this->get('instance_category_manager')->getInstanceList();
        $arrParams['dirs'] = Attachment::$PATH_LIST;

        return $this->render('PortalAdminBundle:AttachmentAdmin:fileUploadForm.html.twig', $arrParams);
    }
}
