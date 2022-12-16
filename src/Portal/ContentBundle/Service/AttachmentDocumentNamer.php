<?php

namespace Portal\ContentBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * Class AttachmentDocumentNamer
 * @package Portal\ContentBundle\Service
 */
class AttachmentDocumentNamer implements NamerInterface
{
    /**
     * @param $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function name($object, PropertyMapping $mapping)
    {
        /** @var $file UploadedFile */
        $file = $mapping->getFile($object);

        return uniqid('', true) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    }
}
