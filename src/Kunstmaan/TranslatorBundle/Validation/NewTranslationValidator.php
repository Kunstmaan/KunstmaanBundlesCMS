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

        $this->validateKeyword($object->getKeyword());
        // TODO : validate all locales, if they are managed by this bundle (config)
        return true;
    }

    public function validateKeyword($keyword)
    {
        $keyword = trim($keyword);

        if ( preg_match('/\s/', $keyword) ) {
            throw new \Exception('A keyword should not contain a whitespace.');
        }

        if (!preg_match('/[A-Za-z\.]/i', $keyword)) {
            throw new \Exception('A keyword should only contain letter or a dot.');
        }
    }
}
