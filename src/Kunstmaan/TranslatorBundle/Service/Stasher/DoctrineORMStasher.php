<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A Stasher to store translations in a ORM database
 */
class DoctrineORMStasher
{

    /**
     * Repository for Translations
     * @var \Kunstmaan\TranslatorBundle\Repository\TranslationRepository
     */
    private $translationRepository;

    public function getAllDomains()
    {
        return $this->translationDomainRepository->findBy(array(), array('name' => 'asc'));
    }







    public function resetTranslationFlags()
    {
        $this->translationRepository->resetAllFlags();
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

}
