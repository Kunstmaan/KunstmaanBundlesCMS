<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\HomePageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ table_name }}")
 */
class HomePage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface, HomePageInterface
{
    public function getDefaultAdminType(): string
    {
        return HomePageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => ContentPage::class,
            ],
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['main'];
    }

    public function getPageTemplates(): array
    {
        return ['homepage'];
    }

    public function getDefaultView(): string
    {
        return 'pages/home_page/view.html.twig';
    }

    public function getSearchType(): string
    {
        return 'Home';
    }
}
