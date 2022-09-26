<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\{{ entity_class }}PageAdminType;
use {{ namespace }}\Repository\{{ entity_class }}PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity(repositoryClass: {{ entity_class }}PageRepository::class)]
#[ORM\Table(name: '{{ prefix }}{{ entity_class|lower }}_pages')]
#[ORM\HasLifecycleCallbacks]
{% else %}
/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}PageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_pages")
 * @ORM\HasLifecycleCallbacks
 */
{% endif %}
class {{ entity_class }}Page extends AbstractArticlePage implements HasPageTemplateInterface, SearchTypeInterface, HideSidebarInNodeEditInterface
{
    //%PagePartial.php.twig%
    public function __construct()
    {
        //%constructor%
    }

    //%PagePartialFunctions.php.twig%
    public function getDefaultAdminType(): string
    {
        return {{ entity_class }}PageAdminType::class;
    }

    public function getSearchType(): string
    {
        return '{{ entity_class }}';
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['{{ entity_class|lower }}main'];
    }

    public function getPageTemplates(): array
    {
        return ['{{ entity_class|lower }}page'];
    }

    public function getDefaultView(): string
    {
        return 'Pages/{{ entity_class }}Page/view.html.twig';
    }

    /**
     * Before persisting this entity, check the date.
     * When no date is present, fill in current date and time.
{% if canUseEntityAttributes == false%}
     *
     * @ORM\PrePersist
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\PrePersist]
{% endif %}
    public function _prePersist(): void
    {
        // Set date to now when none is set
        if (null === $this->date) {
            $this->setDate(new \DateTime());
        }
    }
}
