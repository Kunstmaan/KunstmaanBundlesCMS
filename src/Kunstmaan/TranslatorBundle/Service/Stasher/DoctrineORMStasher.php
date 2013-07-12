<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A Stasher to store translations in a ORM database
 */
class DoctrineORMStasher implements StasherInterface
{

    /**
     * Repository for Translations
     * @var \Kunstmaan\TranslatorBundle\Repository\TranslationRepository
     */
    private $translationRepository;

    /**
     * Repository for TranslationDomains
     * @var \Kunstmaan\TranslatorBundle\Repository\TranslationDomainRepository
     */
    private $translationDomainRepository;

    /**
     * Doctrine ORM entity Manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @{@inheritdoc}
     */
    public function getTranslationDomainsByLocale()
    {
        return $this->translationRepository->getAllDomainsByLocale();
    }

    /**
     * @{@inheritdoc}
     */
    public function getAllDomains()
    {
        return $this->translationDomainRepository->findBy(array(), array('name' => 'asc'));
    }

    /**
     * @{@inheritdoc}
     */
    public function getTranslationGroupsByDomain($domain)
    {
        $translationDomain = $this->translationDomainRepository->findOneBy(array('name' => $domain));

        if (! $translationDomain instanceof TranslationDomain) {
            return array();
        }

        $translationGroups = new ArrayCollection;

        $translations =  $this->translationRepository->findBy(array('domain' => $translationDomain));

        foreach ($translations as $translation) {
            $key = $translation->getKeyword();

            if (!$translationGroups->containsKey($key)) {
                $translationGroup = new TranslationGroup;
                $translationGroup->setDomain($domain);
                $translationGroup->setKeyword($translation->getKeyword());
                $translationGroups->set($key, $translationGroup);
            }

            $translationGroups->get($key)->addTranslation($translation);
        }

        return $translationGroups;
    }

    /**
     * @{@inheritdoc}
     */
    public function updateTranslationGroups(ArrayCollection $groups)
    {
        foreach ($groups as $group) {
            foreach ($group->getTranslations() as $translation) {
                $this->persist($translation);
            }
        }

        return $this->flush();
    }

    /**
     * @{@inheritdoc}
     */
    public function getTranslationGroupByKeywordAndDomain($keyword, $domain)
    {
        $translationDomain = $this->translationDomainRepository->findOneBy(array('name' => $domain));

        if (! $translationDomain instanceof TranslationDomain) {
            return null;
        }

        $translations = $this->translationRepository->findBy(array('keyword' => $keyword, 'domain' => $translationDomain));
        $translationGroup = new TranslationGroup;
        $translationGroup->setDomain($domain);
        $translationGroup->setKeyword($keyword);
        $translationGroup->setTranslations($translations);

        return $translationGroup;
    }

    /**
     * @{@inheritdoc}
     */
    public function getTranslationsByLocaleAndDomain($locale, $domain)
    {
        $translationDomain = $this->translationDomainRepository->findOneBy(array('name' => $domain));

        return $this->translationRepository->findBy(array('locale' => $locale, 'domain' => $translationDomain));
    }

    public function getTranslationsByLocalesAndDomains(array $locales, array $domains)
    {
        return $this->translationRepository->getTranslationsByLocalesAndDomains($locales, $domains);
    }

    /**
     * @{@inheritdoc}
     */
    public function createTranslationDomain($name)
    {
        $domain = new TranslationDomain;
        $domain->setName($name);
        $this->persist($domain);

        return $domain;
    }

    /**
     * @{@inheritdoc}
     */
    public function getDomainByName($name)
    {
        $domain = $this->translationDomainRepository->findOneByName($name);

        return $domain;
    }

    /**
     * @{@inheritdoc}
     */
    public function getLastChangedTranslationDate()
    {
        return $this->translationRepository->getLastChangedTranslationDate();
    }

    /**
     * @{@inheritdoc}
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);

        return $entity;
    }

    /**
     * @{@inheritdoc}
     */
    public function flush($entity = null)
    {
        if ($entity != null) {
            $this->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @{@inheritdoc}
     */
    public function resetTranslationDomainFlags()
    {
        $this->translationDomainRepository->resetAllFlags();
    }

    /**
     * @{@inheritdoc}
     */
    public function resetTranslationFlags()
    {
        $this->translationRepository->resetAllFlags();
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function setTranslationDomainRepository($translationDomainRepository)
    {
        $this->translationDomainRepository = $translationDomainRepository;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
