<?php

namespace Kunstmaan\NodeBundle\Exception;

use Throwable;

class NoNodeTranslationException extends \Exception
{
    /**
     * NoNodeTranslationException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'No Node Translation found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
