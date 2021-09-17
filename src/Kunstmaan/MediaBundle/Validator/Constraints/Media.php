<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Media extends Constraint
{
    public const NOT_FOUND_ERROR = 'b707694b-ff60-4f3f-a1bb-22ecae5b6e0d';
    public const NOT_READABLE_ERROR = 'b4fca756-28db-4c71-adfc-ef2a0c3c3d7c';
    public const EMPTY_ERROR = 'bb60ad41-e149-407f-b52c-35023d123016';
    public const INVALID_MIME_TYPE_ERROR = 'a6afff66-fe95-4f97-8753-de0b0ec9cdf3';
    public const TOO_WIDE_ERROR = '30d779e0-0bb2-48d8-8571-9ce592ff04d3';
    public const TOO_NARROW_ERROR = '2420b5ba-fd3e-4bf4-9f81-a91132ec42a3';
    public const TOO_HIGH_ERROR = '9de7ece8-7837-4a6a-9602-6d0f4d2bd5fb';
    public const TOO_LOW_ERROR = '8833baac-1c7f-402c-96b5-1cf7ac2eb955';

    protected static $errorNames = [
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
        self::NOT_READABLE_ERROR => 'NOT_READABLE_ERROR',
        self::EMPTY_ERROR => 'EMPTY_ERROR',
        self::INVALID_MIME_TYPE_ERROR => 'INVALID_MIME_TYPE_ERROR',
        self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR',
        self::TOO_LOW_ERROR => 'TOO_LOW_ERROR',
        self::TOO_WIDE_ERROR => 'TOO_WIDE_ERROR',
        self::TOO_NARROW_ERROR => 'TOO_NARROW_ERROR',
    ];

    public $minHeight;

    public $maxHeight;

    public $minWidth;

    public $maxWidth;

    public $binaryFormat;

    public $mimeTypes = [];

    public $notFoundMessage = 'The file could not be found.';

    public $notReadableMessage = 'The file is not readable.';

    public $mimeTypesMessage = 'The type of the file is invalid ({{ type }}). Allowed types are {{ types }}.';

    public $disallowEmptyMessage = 'An empty file is not allowed.';

    public $maxWidthMessage = 'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px.';

    public $minWidthMessage = 'The image width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px.';

    public $maxHeightMessage = 'The image height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px.';

    public $minHeightMessage = 'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.';

    public $uploadPartialErrorMessage = 'The file was only partially uploaded.';

    public $uploadNoFileErrorMessage = 'No file was uploaded.';

    public $uploadNoTmpDirErrorMessage = 'No temporary folder was configured in php.ini.';

    public $uploadCantWriteErrorMessage = 'Cannot write temporary file to disk.';

    public $uploadExtensionErrorMessage = 'A PHP extension caused the upload to fail.';

    public $uploadErrorMessage = 'The file could not be uploaded.';
}
