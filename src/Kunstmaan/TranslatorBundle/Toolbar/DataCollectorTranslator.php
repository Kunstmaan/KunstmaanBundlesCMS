<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Symfony\Component\Translation\DataCollectorTranslator as BaseDataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataCollectorTranslator extends BaseDataCollectorTranslator
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
    }
}
