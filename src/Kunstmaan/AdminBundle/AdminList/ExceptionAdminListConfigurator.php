<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Entity\Exception;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;

class ExceptionAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
    }

    public function buildFields()
    {
        $this->addField('code', 'settings.exceptions.code', true);
        $this->addField('url', 'settings.exceptions.url', true);
        $this->addField('urlReferer', 'settings.exceptions.urlReferer', true);
        $this->addField('used', 'settings.exceptions.used', true);
        $this->addField('createdAt', 'settings.exceptions.createdAt', true);
    }

    public function buildFilters()
    {
        $this->addFilter('code', new ORM\NumberFilterType('code'), 'settings.exceptions.code');
        $this->addFilter('url', new ORM\StringFilterType('url'), 'settings.exceptions.url');
        $this->addFilter('urlReferer', new ORM\StringFilterType('urlReferer'), 'settings.exceptions.urlReferer');
        $this->addFilter('isMark', new ORM\BooleanFilterType('isMark'), 'settings.exceptions.isMark');
        $this->addFilter('createdAt', new ORM\DateFilterType('createdAt'), 'settings.exceptions.createdAt');
    }

    public function buildItemActions()
    {
        $this->addItemAction(
            new SimpleItemAction(
                function(Exception $row) {
                    return [
                        'path' => 'kunstmaanadminbundle_admin_exception_toggle_mark',
                        'params' => [
                            'id' => $row->getId()
                        ]
                    ];
                },
                null,
                null,
                '@KunstmaanAdmin/Settings/button_mark.html.twig'
            )
        );
    }

    public function finishQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->orderBy('b.isMark', 'ASC');
        $queryBuilder->addOrderBy('b.createdAt', 'DESC');
    }

    public function canAdd()
    {
        return false;
    }

    public function canExport()
    {
        return false;
    }

    public function canEdit($item)
    {
        return false;
    }

    public function canDelete($item)
    {
        return false;
    }

    public function canView($item)
    {
        return false;
    }

    public function getBundleName()
    {
        return 'KunstmaanAdminBundle';
    }

    public function getEntityName()
    {
        return 'Exception';
    }
}
