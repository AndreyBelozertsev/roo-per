<?php

namespace Portal\HelperBundle\EventListener;

use Vich\UploaderBundle\Event\Event;
use Portal\ContentBundle\Entity\Attachment;

class VichUploadListener
{
    public function onVichUploaderPostUpload(Event $event)
    {
        /** @var Attachment $object */
        $object = $event->getObject();
        $mapping = $event->getMapping();
        $object->setPreviewFileUrl(preg_replace('|([/]+)|s', '/', "{$mapping->getUriPrefix()}/{$mapping->getUploadDir($object)}/{$object->getPreview()}"));
    }
    public function onVichUploaderPreUpload(Event $event)
    {
        /** @var Attachment $object */
        $object = $event->getObject();
        $mapping = $event->getMapping();

        $object->setFileType($object->getFile()->getClientMimeType());
        $object->setOriginalFileName($object->getFile()->getClientOriginalName());
        $object->setFileSize($object->getFile()->getClientSize());
    }
}