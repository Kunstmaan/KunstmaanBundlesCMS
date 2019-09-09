<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleCategoryAdminListConfigurator;

class {{ entity_class }}CategoryAdminListConfigurator extends AbstractArticleCategoryAdminListConfigurator
{
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType({{ entity_class }}CategoryAdminType::class);
    }

    public function getBundleName(): string
    {
        return '{{ bundle.getName() }}';
    }

    public function getEntityName(): string
    {
        return '{{ entity_class }}Category';
    }
}
