<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleCategoryAdminListConfigurator;

/**
 * The AdminList configurator for the {{ entity_class }}Category
 */
class {{ entity_class }}CategoryAdminListConfigurator extends AbstractArticleCategoryAdminListConfigurator
{
    /**
     * @param EntityManagerInterface $em        The entity manager
     * @param AclHelper              $aclHelper The ACL helper
     */
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType({{ entity_class }}CategoryAdminType::class);
    }

    /**
     * Return current bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return '{{ bundle.getName() }}';
    }

    /**
     * Return current entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
	    return '{{ entity_class }}Category';
    }
}
