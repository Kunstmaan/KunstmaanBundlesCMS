<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\File as SystemFile;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class BulkUploadHelper
{

    public $files;

    public function __construct()
    {
        $files = array();
    }

    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }
}

?>