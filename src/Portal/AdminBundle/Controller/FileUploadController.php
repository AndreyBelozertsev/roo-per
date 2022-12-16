<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Attachment;
use Portal\HelperBundle\Helper\AttachmentImageHelper;

class FileUploadController extends Controller
{
    public function uploadAction(Request $request, $instanceCode)
    {
        $file = $request->files->get('upload');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file);
        finfo_close($finfo);
        
        switch (true) {
            // file is image
            case (in_array($fileType, Attachment::IMAGE)):
                $attachmentImageHelper = new AttachmentImageHelper();
                $attachmentImageHelper->resizeImage($file->getPathname(), Attachment::MAX_WIDTH_TEXT_AREAR_IMAGE);
            // file is audio, video, document or archive
            case (in_array($fileType, Attachment::AUDIO)):
            case (in_array($fileType, Attachment::VIDEO)):
            case (in_array($fileType, Attachment::DOCUMENTS)):
            case (in_array($fileType, Attachment::ARCHIVES)):
                $id = $request->query->get('id');
                $path = $this->get('vich_uploader.directory_namer')->createPathDirectoryByAttachment($id);
                $varDir = '/uploads/' . $instanceCode . '/attachments/' . $path;
                $destinationDir = $this->get('kernel')->getProjectDir() . '/web' . $varDir;
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }
                $fileName = '/' . $file->getFilename() . '_' . $file->getClientOriginalName();
                move_uploaded_file($file->getPathname(), $destinationDir . $fileName);
                $resultFilePath = $varDir . $fileName;
                break;

            default:
                $errorMsg = $this->get('translator')->trans('file.wrong_type');
        }

        return $this->render('PortalAdminBundle:FileUpload:file.html.twig', [
            'filePath' => $resultFilePath ?? '',
            'message' => $errorMsg ?? null
        ]);
    }
}
