<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\TranslatorBundle\Entity\Translation;

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

    public function __construct(Connection $connection, array $locales)
    {
        parent::__construct($connection);
        $this->locales = $locales;
        $this->setCountField('CONCAT(t.keyword,t.domain)');
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('domain', new StringFilterType('domain'), 'domain');
        $this->addFilter('keyword', new StringFilterType('keyword'), 'keyword');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('domain', 'Domain', true);
        $this->addField('keyword', 'Keyword', true);
        foreach ($this->locales as $locale) {
            $this->addField($locale, strtoupper($locale), false, 'KunstmaanTranslatorBundle:Translator:inline_edit.html.twig');
        }
    }

    public function canAdd()
    {
        return true;
    }

    public function canEdit($item)
    {
        return false;
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

    public function adaptQueryBuilder(
      QueryBuilder $queryBuilder,
      /** @noinspection PhpUnusedParameterInspection */
      array $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder, $params);

        $queryBuilder
          ->select('DISTINCT CONCAT(t.domain, ":", t.keyword) AS id, t.keyword, t.domain')
          ->from('kuma_translation', 't');

        // Add join for every locale
        foreach ($this->locales as $locale) {
            $queryBuilder->addSelect('t_' . $locale . '.`text` AS ' . $locale);
            $queryBuilder->addSelect('t_' . $locale . '.id AS ' . $locale . '_id');
            $queryBuilder->leftJoin('t', 'kuma_translation', 't_' . $locale,
              't.keyword = t_' . $locale . '.keyword and t.domain = t_' . $locale . '.domain and t_' . $locale . '.locale=:locale_' . $locale);
            $queryBuilder->setParameter('locale_' . $locale, $locale);
        }

        return $queryBuilder;
    }
}