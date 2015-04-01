<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * @Annotation
 */
class Media extends Constraint

{	
    const NOT_FOUND_ERROR = 1;
    const NOT_READABLE_ERROR = 2;
    const EMPTY_ERROR = 3;
    const INVALID_MIME_TYPE_ERROR = 5;
    const TOO_WIDE_ERROR = 11;
    const TOO_HIGH_ERROR = 13;
    const TOO_LOW_ERROR = 14;

    protected static $errorNames = array(
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
        self::NOT_READABLE_ERROR => 'NOT_READABLE_ERROR',
        self::EMPTY_ERROR => 'EMPTY_ERROR',
        self::INVALID_MIME_TYPE_ERROR => 'INVALID_MIME_TYPE_ERROR',
    	self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR',
    	self::TOO_LOW_ERROR => 'TOO_LOW_ERROR',
    	self::TOO_WIDE_ERROR => 'TOO_WIDE_ERROR',
    );

    public $minHeight;
    public $maxHeight;
    public $minWidth;
    public $maxWidth;
    public $binaryFormat;
    public $mimeTypes = array();
    
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
	
	public function __construct($options = null)
	{
		parent::__construct($options);
	
	}
}