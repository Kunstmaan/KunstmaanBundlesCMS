<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\ContentPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ table_name }}")
 */
class ContentPage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface
{
    public function getDefaultAdminType(): string
    {
        return ContentPageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => __CLASS__,
            ],
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['main'];
    }

    public function getPageTemplates(): array
    {
        return ['contentpage'];
    }

    public function getDefaultView(): string
    {
        return 'pages/content_page/view.html.twig';
    }

    public function getSearchType(): string
    {
        return 'Page';
    }
}
