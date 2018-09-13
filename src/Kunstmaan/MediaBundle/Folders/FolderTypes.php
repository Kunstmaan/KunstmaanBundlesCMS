<?php

namespace Kunstmaan\MediaBundle\Folders;

/**
 * Class FolderTypes
 *
 * Enabled folder types.
 *
 * @package Kunstmaan\MediaBundle\Folders
 */
class FolderTypes
{
    const FILES = 'files';
    const IMAGE = 'image';
    const MEDIA = 'media';
    const SLIDESHOW = 'slideshow';
    const VIDEO = 'video';

    public static function allTypes()
    {
        return [
            self::MEDIA => self::MEDIA,
            self::IMAGE => self::IMAGE,
            self::FILES => self::FILES,
            self::SLIDESHOW => self::SLIDESHOW,
            self::VIDEO => self::VIDEO,
        ];
    }
}
