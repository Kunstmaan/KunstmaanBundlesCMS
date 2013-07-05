<?php
namespace Kunstmaan\TranslatorBundle\Repository;

/**
 * Translator Repository class
 */
class TranslationRepository extends AbstractTranslatorRepository
{
    public function getAllDomainsByLocale()
    {
        return $this->createQueryBuilder('t')
            ->select('t.locale, td.name')
            ->leftJoin('t.domain', 'td')
            ->addGroupBy('t.locale')
            ->addGroupBy('td.name')
            ->getQuery()
            ->getArrayResult();
    }
}
