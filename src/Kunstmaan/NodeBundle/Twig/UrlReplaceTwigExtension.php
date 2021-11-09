<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\NodeBundle\Helper\URLHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class UrlReplaceTwigExtension extends AbstractExtension
{
    /**
     * @var URLHelper
     */
    private $urlHelper;

    public function __construct(URLHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('replace_url', [$this, 'replaceUrl']),
        ];
    }

    public function replaceUrl($text)
    {
        return $this->urlHelper->replaceUrl($text);
    }
}
