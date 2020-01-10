<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\NodeBundle\Helper\URLHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @final since 5.4
 */
class UrlReplaceTwigExtension extends AbstractExtension
{
    /**
     * @var URLHelper
     */
    private $urlHelper;

    /**
     * @param URLHelper $urlHelper
     */
    public function __construct(URLHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('replace_url', array($this, 'replaceUrl')),
        );
    }

    public function replaceUrl($text)
    {
        return $this->urlHelper->replaceUrl($text);
    }
}
