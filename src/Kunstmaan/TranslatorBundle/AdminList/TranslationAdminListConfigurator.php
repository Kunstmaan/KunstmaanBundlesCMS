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

    const MAX_TEXT_CHARS = 50;
    const SHOW_TEXT_CHARS = 20;

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
        $this->addField('domain', 'Domain', true);
        $this->addField('keyword', 'Keyword', true);
        $this->addField('locale', 'Locale', true);
        $this->addField('text', 'Text', true);
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

    public function getValue($object, $attribute)
    {
        $value = $object->{'get' . $attribute}();

        if ($object instanceof \Kunstmaan\TranslatorBundle\Entity\Translation && "text" == strtolower($attribute) ) {
            if (mb_strlen($object->getText()) >= self::MAX_TEXT_CHARS) {
                return substr($value, 0, self::SHOW_TEXT_CHARS). " ... " . substr( $value, -1 * self::SHOW_TEXT_CHARS);
            }
        }

        return $value;

    }
}
