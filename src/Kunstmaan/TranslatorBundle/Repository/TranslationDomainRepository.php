<?php
namespace Kunstmaan\TranslatorBundle\Repository;

/**
 * Translator Repository class
 */
class TranslationDomainRepository extends AbstractTranslatorRepository
{
    public function resetAllFlags()
    {
        return $this->createQueryBuilder('t')
            ->update('KunstmaanTranslatorBundle:TranslationDomain', 't')
            ->set('t.flag', "NULL")
            ->getQuery()
            ->execute();

    }
}
