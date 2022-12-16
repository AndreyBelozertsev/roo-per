<?php

namespace Portal\ContentBundle\Service;

use Portal\HelperBundle\Helper\Transliterator;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * Class AttachmentOrignameNamer
 * @package Portal\ContentBundle\Service
 */
class AttachmentOrignameNamer implements NamerInterface
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

        return uniqid('', true).'_'.Transliterator::transliterate(preg_replace('/\s/', '_', $file->getClientOriginalName()));
    }
}
