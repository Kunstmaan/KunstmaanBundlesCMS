<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\EnumerationFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;

/**
 * TranslationAdminListConfigurator
 */
class TranslationAdminListConfigurator extends AbstractDoctrineDBALAdminListConfigurator
{

    /**
     * @var array $locales
     */
    protected $locales;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param Connection $connection
     * @param array $locales
     */
    public function __construct(Connection $connection, array $locales)
    {
        parent::__construct($connection);
        $this->locales = $locales;
        $this->setCountField('CONCAT(b.translation_id)');
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('domain', new StringFilterType('domain'), 'domain');
        $this->addFilter('keyword', new StringFilterType('keyword'), 'keyword');
        $this->addFilter('locale', new EnumerationFilterType('locale'), 'locale', array_combine(
            $this->locales, $this->locales
        ));
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('domain', 'Domain', true);
        $this->addField('keyword', 'Keyword', true);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return false;
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEditInline($item)
    {
        return true;
    }

    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (empty($suffix)) {
            return sprintf('%s_settings_%ss', $this->getBundleName(), strtolower($this->getEntityName()));
        }

        return sprintf('%s_settings_%ss_%s', $this->getBundleName(), strtolower($this->getEntityName()), $suffix);
    }

    /**
     * Get admin type of entity
     *
     * @param mixed $item
     *
     * @return AbstractType|null
     */
    public function getAdminType($item)
    {
        return null;
    }

    public function getBundleName()
    {
        return 'KunstmaanTranslatorBundle';
    }

    public function getEntityName()
    {
        return 'Translation';
    }

    public function getControllerPath()
    {
        return 'KunstmaanTranslatorBundle:Index';
    }

    /**
     * @return QueryBuilder|null
     */
    public function getQueryBuilder()
    {
        if (is_null($this->queryBuilder)) {
            $this->queryBuilder = new QueryBuilder($this->connection);
            // $this->adaptQueryBuilder($this->queryBuilder);
            $this->queryBuilder
              ->select('DISTINCT b.translation_id AS id, b.keyword, b.domain')
              ->from('kuma_translation', 'b');

            // Apply filters
            $filters = $this->getFilterBuilder()->getCurrentFilters();
            $locales = array();

            foreach ($filters as $filter) {
                if ($filter->getType() instanceof EnumerationFilterType) {
                    // Override default enumeration filter handling ... catch selected locales here
                    $data = $filter->getData();
                    $locales = $data['value'];
                } else {
                    /* @var AbstractDBALFilterType $type */
                    $type = $filter->getType();
                    $type->setQueryBuilder($this->queryBuilder);
                    $filter->apply();
                }
            }

            if (!empty($locales)) {
                $this->locales = $locales;
            }

            // Field building hack...
            foreach ($this->locales as $locale) {
                $this->addField($locale, strtoupper($locale), false, 'KunstmaanTranslatorBundle:Translator:inline_edit.html.twig');
            }

            // Field filter hack...
            $this->addFilter('locale', new EnumerationFilterType('locale'), 'locale', array_combine(
                $this->locales, $this->locales
              ));

            // Add join for every locale
            foreach ($this->locales as $locale) {
                $this->queryBuilder->addSelect('t_' . $locale . '.`text` AS ' . $locale);
                $this->queryBuilder->addSelect('t_' . $locale . '.id AS ' . $locale . '_id');
                $this->queryBuilder->leftJoin('b', 'kuma_translation', 't_' . $locale,
                  'b.keyword = t_' . $locale . '.keyword and b.domain = t_' . $locale . '.domain and t_' . $locale . '.locale=:locale_' . $locale);
                $this->queryBuilder->setParameter('locale_' . $locale, $locale);
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->orderBy;
                $this->queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
            }
        }

        return $this->queryBuilder;
    }
}