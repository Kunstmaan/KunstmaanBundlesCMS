<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Symfony\Component\Translation\DataCollectorTranslator as BaseDataCollectorTranslator;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataCollectorTranslator extends BaseDataCollectorTranslator
{
    /**
     * @param TranslatorInterface|LegacyTranslatorInterface $translator The translator must implement TranslatorBagInterface
     */
    public function __construct(/* TranslatorInterface|LegacyTranslatorInterface */ $translator)
    {
        // NEXT_MAJOR Add "Symfony\Contracts\Translation\TranslatorInterface" typehint when sf <4.4 support is removed.
        if (!$translator instanceof \Symfony\Contracts\Translation\TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The "$translator" parameter should be instance of "%s" or "%s"', TranslatorInterface::class, LegacyTranslatorInterface::class));
        }

        parent::__construct($translator);
    }
}
