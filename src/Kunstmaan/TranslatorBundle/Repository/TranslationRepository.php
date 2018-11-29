<?php

namespace Kunstmaan\TranslatorBundle\Repository;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\TextWithLocale;

/**
 * Translator Repository class
 */
class TranslationRepository extends AbstractTranslatorRepository
{
    /**
     * Get an array of all domains group by locales
     *
     * @return array array[0] = ["name" => "messages", "locale" => "nl"]
     */
    public function getAllDomainsByLocale()
    {
        return $this->createQueryBuilder('t')
            ->select('t.locale, t.domain name')
            ->addGroupBy('t.locale')
            ->addGroupBy('t.domain')
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
    SELECT created_at as compare, flag FROM %s
    UNION ALL
    SELECT updated_at as compare, flag FROM %s) CACHE_CHECK
WHERE
    flag IN ('{$flagUpdated}','{$flagNew}')
    GROUP BY flag
    HAVING MAX(compare) IS NOT NULL
    ORDER BY newestDate DESC
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

    public function resetAllFlags()
    {
        return $this->createQueryBuilder('t')
            ->update('KunstmaanTranslatorBundle:Translation', 't')
            ->set('t.flag', 'NULL')
            ->getQuery()
            ->execute();
    }

    public function getTranslationsByLocalesAndDomains($locales, $domains)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb
            ->select('t')
            ->from('KunstmaanTranslatorBundle:Translation', 't')
            ->orderBy('t.domain', 'ASC')
            ->addOrderBy('t.keyword', 'ASC');

        if (count($locales) > 0) {
            $qb->andWhere($qb->expr()->in('t.locale', $locales));
        }

        if (count($domains) > 0) {
            $qb->andWhere($qb->expr()->in('t.domain', $domains));
        }

        $result = $qb
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function flush($entity = null)
    {
        if ($entity !== null) {
            $this->persist($entity);
        }

        return $this->getEntityManager()->flush();
    }

    public function persist($entity)
    {
        return $this->getEntityManager()->persist($entity);
    }

    public function isUnique(\Kunstmaan\TranslatorBundle\Model\Translation $translationModel)
    {
        $qb = $this->createQueryBuilder('t');
        $count = $qb->select('COUNT(t.id)')
            ->where('t.domain = :domain')
            ->andWhere('t.keyword = :keyword')
            ->setParameter('domain', $translationModel->getDomain())
            ->setParameter('keyword', $translationModel->getKeyword())
            ->getQuery()
            ->getSingleScalarResult();

        return $count == 0;
    }

    public function createTranslations(\Kunstmaan\TranslatorBundle\Model\Translation $translationModel)
    {
        $this->getEntityManager()->beginTransaction();

        try {
            // Fetch new translation ID
            $translationId = $this->getUniqueTranslationId();
            /*
             * @var TextWithLocale
             */
            foreach ($translationModel->getTexts() as $textWithLocale) {
                $text = $textWithLocale->getText();
                if (empty($text)) {
                    continue;
                }

                $translation = new Translation();
                $translation
                    ->setDomain($translationModel->getDomain())
                    ->setKeyword($translationModel->getKeyword())
                    ->setTranslationId($translationId)
                    ->setLocale($textWithLocale->getLocale())
                    ->setText($textWithLocale->getText());
                $this->getEntityManager()->persist($translation);
            }
            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
        }
    }

    public function updateTranslations(\Kunstmaan\TranslatorBundle\Model\Translation $translationModel, $translationId)
    {
        $this->getEntityManager()->beginTransaction();

        try {
            /*
             * @var TextWithLocale
             */
            foreach ($translationModel->getTexts() as $textWithLocale) {
                if ($textWithLocale->getId()) {
                    $translation = $this->find($textWithLocale->getId());
                    $translation->setLocale($textWithLocale->getLocale())
                        ->setText($textWithLocale->getText());
                    $this->getEntityManager()->persist($translation);
                } else {
                    $text = $textWithLocale->getText();
                    if (empty($text)) {
                        continue;
                    }

                    $translation = new Translation();
                    $translation
                        ->setDomain($translationModel->getDomain())
                        ->setKeyword($translationModel->getKeyword())
                        ->setTranslationId($translationId)
                        ->setLocale($textWithLocale->getLocale())
                        ->setText($textWithLocale->getText());
                    $this->getEntityManager()->persist($translation);
                }
            }
            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
        }
    }

    /**
     * Removes all translations with the given translation id
     *
     * @param string $translationId
     *
     * @return mixed
     */
    public function removeTranslations($translationId)
    {
        return $this->createQueryBuilder('t')
            ->delete()
            ->where('t.translationId = :translationId')
            ->setParameter('translationId', $translationId)
            ->getQuery()
            ->execute();
    }

    public function getUniqueTranslationId()
    {
        $qb = $this->createQueryBuilder('t');
        $newId = $qb->select('MAX(t.translationId)+1')
            ->getQuery()
            ->getSingleScalarResult();
        if (is_null($newId)) {
            $newId = 1;
        }

        return $newId;
    }
}
