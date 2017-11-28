<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Trait TranslatorTrait
 */
trait TranslatorTrait
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        if (null !== $this->container && null === $this->translator) {
            $this->translator = $this->container->get("translator");
        }

        return $this->translator;
    }

    /**
     * @required
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }
}
