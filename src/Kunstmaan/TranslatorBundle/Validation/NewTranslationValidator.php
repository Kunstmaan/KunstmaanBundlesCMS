<?php

namespace Kunstmaan\TranslatorBundle\Validation;

use Kunstmaan\TranslatorBundle\Model\Translation\NewTranslation;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class NewTranslationValidator implements ValidatorInterface
{

    public function validate($object)
    {
        if (!$object instanceof NewTranslation) {
            throw new InvalidArgumentException('$object should be an instace of class NewTranslationValidator');
        }

        return true;
    }
}
