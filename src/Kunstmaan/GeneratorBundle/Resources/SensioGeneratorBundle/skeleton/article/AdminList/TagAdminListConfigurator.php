<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use {{ namespace }}\Form\{{ entity_class }}TagAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleTagAdminListConfigurator;
/**
 * The AdminList configurator for the {{ entity_class }}Tag
 */
class {{ entity_class }}TagAdminListConfigurator extends AbstractArticleTagAdminListConfigurator
{
    /**
     * @param EntityManagerInterface $em        The entity manager
     * @param AclHelper              $aclHelper The ACL helper
     */
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new {{ entity_class }}TagAdminType());
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
	    return '{{ entity_class }}Tag';
    }
}
