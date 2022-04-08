<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\LegalPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\CookieBundle\ViewDataProvider\LegalPageViewDataProvider;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}legal_pages")
 */
class LegalPage extends AbstractPage implements HasPageTemplateInterface, CustomViewDataProviderInterface
{
    public function getDefaultAdminType(): string
    {
        return LegalPageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return [
            '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legal_header',
            '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legal_main',
        ];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legalpage'];
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/LegalPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    public function getViewDataProviderServiceId(): string
    {
        return LegalPageViewDataProvider::class;
    }
}
