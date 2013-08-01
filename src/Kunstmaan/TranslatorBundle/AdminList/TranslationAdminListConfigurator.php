<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Entity\Translation;

use Doctrine\ORM\QueryBuilder;

/**
 * TranslationAdminListConfigurator
 */
class TranslationAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @var string
     */
    protected $locale;

    protected $query;

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('text', new StringFilterType('text'), 'Text');
        $this->addFilter('domain', new StringFilterType('name', 'd'), 'domain');
        $this->addFilter('keyword', new StringFilterType('keyword'), 'keyword');
        $this->addFilter('locale', new StringFilterType('locale'), 'locale');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('domain', 'domain', true);
        $this->addField('keyword', 'keyword', true);
        $this->addField('locale', 'locale', true);
        $this->addField('text', 'Text', true);
        // $this->addField('title', 'Title', true, 'KunstmaanNodeBundle:Admin:title.html.twig')
        //     ->addField('created', 'Created At', false)
        //     ->addField('updated', 'Updated At', false)
        //     ->addField('online', 'Online', true, 'KunstmaanNodeBundle:Admin:online.html.twig');
    }



    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * Return if current user can delete the specified item
     *
     * @param array|object $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return true;
    }


    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanTranslatorBundle';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'Translation';
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
            return sprintf('%s_translations', $this->getBundleName());
        }

        return sprintf('%s_translations_%s', $this->getBundleName(), $suffix);
    }

    /**
     * Override controller path (because actions for different entities are defined in a single Settings controller).
     *
     * @return string
     */
    public function getControllerPath()
    {
        return 'KunstmaanTranslatorBundle:Index';
    }



}
