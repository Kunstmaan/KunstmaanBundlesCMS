<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\File as SystemFile;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

/**
 * BulkUploadHelper
 */
class BulkUploadHelper
{

    /**
     * @var Media[]
     */
    public $files = array();

    /**
     * @param Media[] $files
     *
     * @return BulkUploadHelper
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return Media[]
     */
    public function getFiles()
    {
        return $this->files;
    }
}