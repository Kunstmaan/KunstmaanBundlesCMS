<?php

namespace Kunstmaan\NodeBundle\Exception;

use Throwable;

class NoNodeTranslationException extends \Exception
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message = 'No Node Translation found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
