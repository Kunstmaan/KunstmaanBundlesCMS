<?php

namespace Kunstmaan\ArticleBundle\PagePartAdmin;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\ArticleBundle\PagePartAdmin\Traits\ArticleConfiguratorTrait;

/**
 * The PagePartAdminConfigurator for the AbstractArticleOverviewPage
 */
class AbstractArticleOverviewPagePagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{
    use ArticleConfiguratorTrait;
}
