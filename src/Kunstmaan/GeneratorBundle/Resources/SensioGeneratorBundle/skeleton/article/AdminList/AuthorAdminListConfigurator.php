<?php

namespace {{ namespace }}\AdminList;

use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;

/**
 * The AdminList configurator for the {{ entity_class }}Author
 */
class {{ entity_class }}AuthorAdminListConfigurator extends AbstractArticleAuthorAdminListConfigurator
{
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
	return '{{ entity_class }}Author';
    }
}
