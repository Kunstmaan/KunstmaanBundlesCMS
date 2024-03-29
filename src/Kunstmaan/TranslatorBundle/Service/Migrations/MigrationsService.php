<?php

namespace Kunstmaan\TranslatorBundle\Service\Migrations;

use Doctrine\ORM\EntityManager;
use Kunstmaan\TranslatorBundle\Entity\Translation;

class MigrationsService
{
    /**
     * Repository for Translations
     *
     * @var \Kunstmaan\TranslatorBundle\Repository\TranslationRepository
     */
    private $translationRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function getDiffSqlArray()
    {
        $sql = [];
        $inserts = $this->getNewTranslationSql();
        $updates = $this->getUpdatedTranslationSqlArray();

        if ($inserts != '' && !\is_null($inserts)) {
            $sql[] = $inserts;
        }

        if (\count($updates) > 0 && \is_array($updates)) {
            $sql = array_merge($sql, $updates);
        }

        return $sql;
    }

    public function getUpdatedTranslationSqlArray()
    {
        $ignoreFields = ['id'];
        $uniqueKeys = ['domain', 'locale', 'keyword'];

        $translations = $this->translationRepository->findBy(['flag' => \Kunstmaan\TranslatorBundle\Entity\Translation::FLAG_UPDATED]);

        if (\count($translations) <= 0) {
            return [];
        }

        $fieldNames = $this->entityManager->getClassMetadata(Translation::class)->getFieldNames();
        $tableName = $this->entityManager->getClassMetadata(Translation::class)->getTableName();
        $tableName = $this->entityManager->getConnection()->quoteIdentifier($tableName);

        $fieldNames = array_diff($fieldNames, $ignoreFields, $uniqueKeys);

        $sql = [];

        foreach ($translations as $translation) {
            $updateValues = [];
            $whereValues = [];

            foreach ($fieldNames as $fieldName) {
                $value = $translation->{'get' . $fieldName}();
                $columnName = $this->entityManager->getClassMetadata(Translation::class)->getColumnName($fieldName);
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }

                $updateValues[] = $this->entityManager->getConnection()->quoteIdentifier($columnName) . ' = ' . $this->entityManager->getConnection()->quote($value);
            }

            foreach ($uniqueKeys as $uniqueKey) {
                $value = $translation->{'get' . $uniqueKey}();

                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }

                $whereValues[] = $this->entityManager->getConnection()->quoteIdentifier($uniqueKey) . ' = ' . $this->entityManager->getConnection()->quote($value);
            }

            $sql[] = sprintf('UPDATE %s SET %s WHERE %s', $tableName, implode(',', $updateValues), implode(' AND ', $whereValues));
        }

        return $sql;
    }

    /**
     * Build an sql insert into query by the paramters provided
     *
     * @param ORM\Entity $entities        Result array with all entities to create an insert for
     * @param string     $entityClassName Class of the specified entity (same as entities)
     * @param array      $ignoreFields    fields not to use in the insert query
     *
     * @return string an insert sql query, of no result nul
     */
    public function buildInsertSql($entities, $entityClassName, $ignoreFields = [])
    {
        if (\count($entities) <= 0) {
            return null;
        }

        $fieldNames = $this->entityManager->getClassMetadata($entityClassName)->getFieldNames();

        $tableName = $this->entityManager->getClassMetadata($entityClassName)->getTableName();
        $tableName = $this->entityManager->getConnection()->quoteIdentifier($tableName);
        $fieldNames = array_diff($fieldNames, $ignoreFields);

        $values = [];

        foreach ($entities as $entity) {
            $insertValues = [];

            foreach ($fieldNames as $fieldName) {
                $value = $entity->{'get' . $fieldName}();

                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }

                $insertValues[] = $this->entityManager->getConnection()->quote($value);
            }

            $values[] = '(' . implode(',', $insertValues) . ')';
        }

        foreach ($fieldNames as $key => $fieldName) {
            $columnName = $this->entityManager->getClassMetadata($entityClassName)->getColumnName($fieldName);
            $fieldNames[$key] = $this->entityManager->getConnection()->quoteIdentifier($columnName);
        }

        $sql = sprintf('INSERT INTO %s (%s) VALUES %s', $tableName, implode(',', $fieldNames), implode(', ', $values));

        return $sql;
    }

    /**
     * Get the sql query for all new translations added
     *
     * @return string sql query
     */
    public function getNewTranslationSql()
    {
        $translations = $this->translationRepository->findBy(['flag' => \Kunstmaan\TranslatorBundle\Entity\Translation::FLAG_NEW]);

        return $this->buildInsertSql($translations, '\Kunstmaan\TranslatorBundle\Entity\Translation', ['flag', 'id']);
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
