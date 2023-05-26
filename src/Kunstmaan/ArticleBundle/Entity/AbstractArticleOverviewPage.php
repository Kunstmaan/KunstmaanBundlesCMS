<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use Kunstmaan\ArticleBundle\ViewDataProvider\ArticlePageViewDataProvider;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

/**
 * The article overview page which shows its articles
 */
abstract class AbstractArticleOverviewPage extends AbstractPage implements HasPagePartsInterface, CustomViewDataProviderInterface
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }

    public function getPagePartAdminConfigurations()
    {
        return [new AbstractArticleOverviewPagePagePartAdminConfigurator()];
    }

    /**
     * Return the Article repository
     */
    abstract public function getArticleRepository(EntityManagerInterface $em);

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '@KunstmaanArticle/AbstractArticleOverviewPage/view.html.twig';
    }

    public function getViewDataProviderServiceId(): string
    {
        return ArticlePageViewDataProvider::class;
    }
}
