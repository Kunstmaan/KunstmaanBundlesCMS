<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminBundle\AdminList\AbstractSettingsAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Entity\Translation;

/**
 * TranslationAdminListConfigurator
 */
class TranslationAdminListConfigurator extends AbstractSettingsAdminListConfigurator
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
        $this->addFilter('text', new StringFilterType('text'), 'text');
        $this->addFilter('domain', new StringFilterType('domain'), 'domain');
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

    public function canAdd()
    {
        return true;
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
}
