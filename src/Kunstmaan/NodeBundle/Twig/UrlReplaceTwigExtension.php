<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\NodeBundle\Helper\URLHelper;

class UrlReplaceTwigExtension extends \Twig_Extension
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
            new \Twig_SimpleFilter('replace_url', array($this, 'replaceUrl')),
        );
    }

    public function replaceUrl($text)
    {
        return $this->urlHelper->replaceUrl($text);
    }
}
