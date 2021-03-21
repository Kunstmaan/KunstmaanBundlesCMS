<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ChangeableLimitInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\EnumerationFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use Kunstmaan\AdminListBundle\Traits\ChangeableLimitTrait;
use Kunstmaan\TranslatorBundle\Entity\Translation;

/**
 * TranslationAdminListConfigurator
 */
class TranslationAdminListConfigurator extends AbstractDoctrineDBALAdminListConfigurator implements ChangeableLimitInterface
{
    use ChangeableLimitTrait;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Field[]
     */
    private $exportFields = [];

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
        $this->addFilter('status', new StringFilterType('status'), 'kuma_translator.adminlist.filter.status');
        $this->addFilter('domain', new StringFilterType('domain'), 'kuma_translator.adminlist.filter.domain');
        $this->addFilter('keyword', new StringFilterType('keyword'), 'kuma_translator.adminlist.filter.keyword');
        $this->addFilter('text', new StringFilterType('text'), 'kuma_translator.adminlist.filter.text');
        $this->addFilter('locale', new EnumerationFilterType('locale'), 'kuma_translator.adminlist.filter.locale', array_combine(
            $this->locales,
            $this->locales
        ));
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('domain', 'kuma_translator.adminlist.header.domain', true);
        $this->addField('keyword', 'kuma_translator.adminlist.header.keyword', true);
        $this->addField('status', 'kuma_translator.adminlist.header.status', true);
    }

    public function getExportFields()
    {
        if (empty($this->exportFields)) {
            $this->addExportField('domain', 'kuma_translator.adminlist.header.domain');
            $this->addExportField('keyword', 'kuma_translator.adminlist.header.keyword');

            $this->locales = array_unique($this->locales);
            // Field building hack...
            foreach ($this->locales as $locale) {
                $this->addExportField($locale, strtoupper($locale));
            }

            $this->addExportField('status', 'kuma_translator.adminlist.header.status');
        }

        return $this->exportFields;
    }

    public function addExportField($name, $header, $template = null, FieldAlias $alias = null)
    {
        $this->exportFields[] = new Field($name, $header);

        return $this;
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
     * @return bool
     */
    public function canExport()
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
     * {@inheritdoc}
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
        if (\is_null($this->queryBuilder)) {
            $this->queryBuilder = new QueryBuilder($this->connection);
            $this->queryBuilder
                ->select('DISTINCT b.translation_id AS id, b.keyword, b.domain, b.status')
                ->from('kuma_translation', 'b')
                ->andWhere('b.status != :statusstring')
                ->setParameter('statusstring', Translation::STATUS_DISABLED);

            // Apply filters
            $filters = $this->getFilterBuilder()->getCurrentFilters();
            $locales = [];

            $textValue = $textComparator = null;
            foreach ($filters as $filter) {
                if ($filter->getType() instanceof EnumerationFilterType && $filter->getColumnName() == 'locale') {
                    // Override default enumeration filter handling ... catch selected locales here
                    $data = $filter->getData();
                    $comparator = $filter->getType()->getComparator();
                    if ($comparator == 'in') {
                        $locales = $data['value'];
                    } elseif ($comparator == 'notin') {
                        $locales = array_diff($this->locales, $data['value']);
                    }
                } elseif ($filter->getType() instanceof StringFilterType && $filter->getColumnName() == 'text') {
                    // Override default text filter handling ...
                    $data = $filter->getData();
                    $textValue = $data['value'];
                    $textComparator = $data['comparator'];
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
            $this->locales = array_unique($this->locales);

            // Field building hack...
            foreach ($this->locales as $locale) {
                $this->addField($locale, strtoupper($locale), false, '@KunstmaanTranslator/Translator/inline_edit.html.twig');
            }

            // Field filter hack...
            $this->addFilter('locale', new EnumerationFilterType('locale'), 'kuma_translator.adminlist.filter.locale', array_combine(
                $this->locales,
                $this->locales
            ));

            $identifierQuoteCharacter = $this->connection->getDatabasePlatform()->getIdentifierQuoteCharacter();
            $quotedTextColumnName = $identifierQuoteCharacter . 'text' . $identifierQuoteCharacter;

            // Add join for every locale
            foreach ($this->locales as $locale) {
                $this->queryBuilder->addSelect('t_' . $locale . '.' . $quotedTextColumnName . ' AS ' . $locale);
                $this->queryBuilder->addSelect('t_' . $locale . '.id AS ' . $locale . '_id');
                $this->queryBuilder->leftJoin(
                    'b',
                    'kuma_translation',
                    't_' . $locale,
                    'b.keyword = t_' . $locale . '.keyword and b.domain = t_' . $locale . '.domain and t_' . $locale . '.locale=:locale_' . $locale
                );
                $this->queryBuilder->setParameter('locale_' . $locale, $locale);
            }

            // Apply text filter
            if (!\is_null($textValue) && !\is_null($textComparator)) {
                $orX = $this->queryBuilder->expr()->orX();

                foreach ($this->locales as $key => $locale) {
                    $uniqueId = 'txt_' . $key;
                    $expr = null;
                    switch ($textComparator) {
                        case 'equals':
                            $expr = $this->queryBuilder->expr()->eq('t_' . $locale . '.' . $quotedTextColumnName, ':var_' . $uniqueId);
                            $this->queryBuilder->setParameter('var_' . $uniqueId, $textValue);

                            break;
                        case 'notequals':
                            $expr = $this->queryBuilder->expr()->neq('t_' . $locale . '.' . $quotedTextColumnName, ':var_' . $uniqueId);
                            $this->queryBuilder->setParameter('var_' . $uniqueId, $textValue);

                            break;
                        case 'contains':
                            $expr = $this->queryBuilder->expr()->like('t_' . $locale . '.' . $quotedTextColumnName, ':var_' . $uniqueId);
                            $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $textValue . '%');

                            break;
                        case 'doesnotcontain':
                            $expr = 't_' . $locale . '.' . $quotedTextColumnName . ' NOT LIKE :var_' . $uniqueId;
                            $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $textValue . '%');

                            break;
                        case 'startswith':
                            $expr = $this->queryBuilder->expr()->like('t_' . $locale . '.' . $quotedTextColumnName, ':var_' . $uniqueId);
                            $this->queryBuilder->setParameter('var_' . $uniqueId, $textValue . '%');

                            break;
                        case 'endswith':
                            $expr = $this->queryBuilder->expr()->like('t_' . $locale . '.' . $quotedTextColumnName, ':var_' . $uniqueId);
                            $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $textValue);

                            break;
                        case 'empty':
                            $expr = $this->queryBuilder->expr()->orX(
                                $this->queryBuilder->expr()->isNull('t_' . $locale . '.' . $quotedTextColumnName),
                                $this->queryBuilder->expr()->eq('t_' . $locale . '.' . $quotedTextColumnName, '\'-\''),
                                $this->queryBuilder->expr()->eq('t_' . $locale . '.' . $quotedTextColumnName, '\'\'')
                            );

                            break;
                    }

                    if (null !== $expr) {
                        $orX->add($expr);
                    }
                }

                $this->queryBuilder->andWhere($orX);
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
