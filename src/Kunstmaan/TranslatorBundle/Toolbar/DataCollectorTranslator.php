<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\DataCollectorTranslator as BaseDataCollectorTranslator;

/**
 * Class DataCollectorTranslator
 */
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
