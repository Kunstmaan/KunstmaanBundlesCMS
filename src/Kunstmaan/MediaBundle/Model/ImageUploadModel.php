<?php

declare(strict_types=1);

namespace Kunstmaan\MediaBundle\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/** @internal  */
final class ImageUploadModel
{
    /**
     * @var File
     * @Assert\Image(detectCorrupted=true)
     */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file)
    {
        $this->file = $file;

        return $this;
    }
}
