<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * BulkUploadHelper
 */
class BulkUploadHelper
{

    /**
     * @var UploadedFile[]
     */
    public $files = array();

    /**
     * @param UploadedFile[] $files
     *
     * @return BulkUploadHelper
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return UploadedFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }
}