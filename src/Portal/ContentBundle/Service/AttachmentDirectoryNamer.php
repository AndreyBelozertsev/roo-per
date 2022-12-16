<?php

namespace Portal\ContentBundle\Service;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * AttachmentDirectoryNamer
 */
class AttachmentDirectoryNamer implements DirectoryNamerInterface
{
    /**
     * @param PropertyMapping $mapping
     * @return mixed
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        return $this->createPathDirectory($object);
    }

    /**
     *
     * @return mixed
     */
    public function createPathDirectory($object)
    {
        return $this->createPathDirectoryByAttachment($object);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function createPathDirectoryByAttachment($id)
    {
        // Create md5 from Doc Code
        $md5 = md5($id);
        // Reqular Expression [\w]{2}/[\w]{2}/[\w]{2}/*/
        $directoryFolder = preg_replace("/([\w]{2})([\w]{2})([\w]{2})(.*)/", "$1/$2/$3/$4", $md5);

        return $directoryFolder;
    }
}
