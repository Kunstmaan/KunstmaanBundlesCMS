<?php

namespace {{ namespace }}\AdminList;

use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;

class {{ entity_class }}AuthorAdminListConfigurator extends AbstractArticleAuthorAdminListConfigurator
{
    public function getBundleName(): string
    {
        return '{{ bundle.getName() }}';
    }

    public function getEntityName(): string
    {
        return '{{ entity_class }}Author';
    }
}
