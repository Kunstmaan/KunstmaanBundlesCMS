<?php

namespace Kunstmaan\Rest\TranslationsBundle\Model\Exception;

use Exception;

class TranslationException extends Exception
{
    const NOT_VALID = 'request body does not contain expected translation';
}
