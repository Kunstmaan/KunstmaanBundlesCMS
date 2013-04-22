<?php

namespace Kunstmaan\ArticleBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;

/**
 * The AdminList configurator for the AbstractArticlePage
 */
class AbstractArticlePageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator {

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
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
        return "AbstractArticlePage";
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("title", "Title", true);
    }
}