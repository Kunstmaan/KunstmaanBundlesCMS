<?php

namespace {{ namespace }}\AdminList;

use {{ namespace }}\Entity\{{ entity_class }}Author;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;

class {{ entity_class }}AuthorAdminListConfigurator extends AbstractArticleAuthorAdminListConfigurator
{
    public function getEntityClass(): string
    {
        return {{ entity_class }}Author::class;
    }
}
