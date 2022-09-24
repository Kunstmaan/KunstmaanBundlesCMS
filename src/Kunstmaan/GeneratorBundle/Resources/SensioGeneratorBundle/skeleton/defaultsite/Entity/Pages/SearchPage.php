<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}search_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}search_pages")
 */
{% endif %}
class SearchPage extends AbstractSearchPage implements HasPageTemplateInterface
{
    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/SearchPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}main'];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}searchpage'];
    }
}
