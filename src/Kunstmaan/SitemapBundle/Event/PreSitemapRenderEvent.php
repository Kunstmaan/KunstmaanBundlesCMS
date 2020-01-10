<?php

namespace Kunstmaan\SitemapBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kunstmaan\SitemapBundle\Model\SitemapUrl;
use Symfony\Component\EventDispatcher\Event;

final class PreSitemapRenderEvent extends Event
{
    public const NAME = 'sitemap.pre_render';

    /** @var ArrayCollection */
    private $extraItems;

    /** @var string */
    private $locale;

    public function __construct($locale)
    {
        $this->extraItems = new ArrayCollection();
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getExtraItems(): Collection
    {
        return $this->extraItems;
    }

    public function addExtraItem(SitemapUrl $extraItem): void
    {
        if (!$this->extraItems->contains($extraItem)) {
            $this->extraItems->add($extraItem);
        }
    }
}
