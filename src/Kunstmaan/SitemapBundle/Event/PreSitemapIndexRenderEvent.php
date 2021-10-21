<?php

namespace Kunstmaan\SitemapBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\SitemapBundle\Model\SitemapIndex;

final class PreSitemapIndexRenderEvent extends BcEvent
{
    public const NAME = 'sitemap.index.pre_render';

    /**
     * @var ArrayCollection<SitemapIndex>
     */
    private $extraSitemaps;

    /**
     * @var array
     */
    private $locales;

    public function __construct(array $locales)
    {
        $this->extraSitemaps = new ArrayCollection();
        $this->locales = $locales;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function getExtraSitemaps(): Collection
    {
        return $this->extraSitemaps;
    }

    public function addExtraSitemap(SitemapIndex $sitemapIndex): void
    {
        if (!$this->extraSitemaps->contains($sitemapIndex)) {
            $this->extraSitemaps->add($sitemapIndex);
        }
    }
}
