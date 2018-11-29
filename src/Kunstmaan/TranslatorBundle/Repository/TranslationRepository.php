<?php

namespace Kunstmaan\TranslatorBundle\Repository;

use DateTime;
use Exception;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation as TranslationModel;
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

    /**
     * Get an array of all non disabled translations
     *
     * @param string $locale
     * @param string $domain
     *
     * @return array
     */
    public function findAllNotDisabled($locale, $domain = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select('t')
            ->where('t.locale = :locale')
            ->andWhere('t.status != :statusstring')
            ->setParameter('statusstring', Translation::STATUS_DISABLED)
            ->setParameter('locale', $locale);
        if (!\is_null($domain) && !empty($domain)) {
            $qb->andWhere('t.domain = :tdomain')
                ->setParameter('tdomain', $domain);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return DateTime|null
     */
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

    /**
     * @return mixed
     */
    public function resetAllFlags()
    {
        return $this->createQueryBuilder('t')
            ->update('KunstmaanTranslatorBundle:Translation', 't')
            ->set('t.flag', 'NULL')
            ->getQuery()
            ->execute();
    }

    /**
     * @param $locales
     * @param $domains
     *
     * @return mixed
     */
    public function getTranslationsByLocalesAndDomains($locales, $domains)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb
            ->select('t')
            ->from('KunstmaanTranslatorBundle:Translation', 't')
            ->andWhere('t.status != :statusstring')
            ->setParameter('statusstring', Translation::STATUS_DISABLED)
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

    /**
     * @param null $entity
     *
     * @return mixed
     */
    public function flush($entity = null)
    {
        if ($entity !== null) {
            $this->persist($entity);
        }

        return $this->getEntityManager()->flush();
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function persist($entity)
    {
        return $this->getEntityManager()->persist($entity);
    }

    /**
     * @param TranslationModel $translationModel
     *
     * @return bool
     */
    public function isUnique(TranslationModel $translationModel)
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

    /**
     * @param TranslationModel $translationModel
     */
    public function createTranslations(TranslationModel $translationModel)
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
        } catch (Exception $e) {
            $this->getEntityManager()->rollback();
        }
    }

    /**
     * @param TranslationModel $translationModel
     * @param                  $translationId
     */
    public function updateTranslations(TranslationModel $translationModel, $translationId)
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
        } catch (Exception $e) {
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

    /**
     * @return int
     */
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

    /**
     * @param DateTime $date
     * @param string   $domain
     *
     * @return mixed
     */
    public function findDeprecatedTranslationsBeforeDate(DateTime $date, $domain)
    {
        $qb = $this->createQueryBuilder('t');
        $result = $qb->select('t')
            ->where('t.status = :status')
            ->andWhere('t.domain = :domain')
            ->andWhere('t.updatedAt < :date')
            ->setParameter('status', Translation::STATUS_DEPRECATED)
            ->setParameter('domain', $domain)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
