<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Symfony\Component\Translation\DataCollectorTranslator as BaseDataCollectorTranslator;
use Symfony\Component\Translation\TranslatorInterface;

class DataCollectorTranslator extends BaseDataCollectorTranslator
{
    /**
     * @param TranslatorInterface $translator The translator must implement TranslatorBagInterface
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
    }
}
