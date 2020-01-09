<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use {{ namespace }}\Form\{{ entity_class }}TagAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleTagAdminListConfigurator;

class {{ entity_class }}TagAdminListConfigurator extends AbstractArticleTagAdminListConfigurator
{
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType({{ entity_class }}TagAdminType::class);
    }

    public function getBundleName(): string
    {
        return '{{ bundle.getName() }}';
    }

    public function getEntityName(): string
    {
        return '{{ entity_class }}Tag';
    }
}
