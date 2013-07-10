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

    public function getLastChangedTranslationDate()
    {
        $em = $this->getEntityManager();

        $flagNew = \Kunstmaan\TranslatorBundle\Entity\Translation::FLAG_NEW;
        $flagUpdated = \Kunstmaan\TranslatorBundle\Entity\Translation::FLAG_UPDATED;

        $sql = <<<EOQ
SELECT
    MAX(compare) as newestDate,
    flag
FROM (
    SELECT createdAt as compare, flag FROM %s
    UNION ALL
    SELECT updatedAt as compare, flag FROM %s) CACHE_CHECK
WHERE
    flag IN ('{$flagUpdated}','{$flagNew}')
    GROUP BY flag
    HAVING MAX(compare) IS NOT NULL
EOQ;
        $table = $em->getClassMetaData('KunstmaanTranslatorBundle:Translation')->getTableName();

        $stmt = $em->getConnection()->prepare(sprintf($sql, $table, $table));
        $stmt->execute();
        $result = $stmt->fetch();

        if (is_array($result) && count($result) > 0) {
            return new \DateTime($result['newestDate']);
        }

        return null;
    }
}
