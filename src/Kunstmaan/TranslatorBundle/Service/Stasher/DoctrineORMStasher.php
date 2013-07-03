<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Doctrine\Common\Collections\ArrayCollection;

class DoctrineORMStasher implements StasherInterface
{

    private $translationRepository;
    private $translationDomainRepository;
    private $entityManager;

    public function getTranslationDomainsByLocale()
    {
        return $this->translationRepository->getAllDomainsByLocale();
    }

    public function getAllDomains()
    {
        return $this->translationDomainRepository->findAll();
    }

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

    public function getTranslationsByLocaleAndDomain($locale, $domain)
    {
        $translationDomain = $this->translationDomainRepository->findOneBy(array('name' => $domain));

        return $this->translationRepository->findBy(array('locale' => $locale, 'domain' => $translationDomain));
    }

    public function createTranslationDomain($name)
    {
        $domain = new TranslationDomain;
        $domain->setName($name);
        $this->persist($domain);
        return $domain;
    }

    public function getDomainByName($name)
    {
        $domain = $this->translationDomainRepository->findOneByName($name);

        return $domain;
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function setTranslationDomainRepository($translationDomainRepository)
    {
        $this->translationDomainRepository = $translationDomainRepository;
    }

    public function persist($entity)
    {
        $this->entityManager->persist($entity);
        return $entity;
    }

    public function flush($entity = null)
    {
        if($entity != null) {
            $this->persist($entity);
        }

        $this->entityManager->flush();
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