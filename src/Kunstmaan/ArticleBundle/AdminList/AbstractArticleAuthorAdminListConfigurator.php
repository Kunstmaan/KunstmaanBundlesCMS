<?php

namespace Kunstmaan\ArticleBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * The AdminList configurator for the AbstractArticleAuthor
 */
class AbstractArticleAuthorAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $permission;

    /**
     * @param EntityManager $em         The entity manager
     * @param AclHelper     $aclHelper  The ACL helper
     * @param string        $locale     The current locale
     * @param string        $permission The permission
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper, $locale, $permission)
    {
        parent::__construct($em, $aclHelper);
        $this->locale = $locale;
        $this->setPermissionDefinition(
            new PermissionDefinition(array($permission), 'Kunstmaan\NodeBundle\Entity\Node', 'n')
        );
    }

    /**
     * Return current bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return "KunstmaanArticleBundle";
    }

    /**
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return "AbstractArticleAuthor";
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'Name');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('title', 'Title', true, 'KunstmaanNodeBundle:Admin:title.html.twig');
    }

}
