<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class MediaTokenTransformer.
 */
class MediaTokenTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $content
     *
     * @return string
     */
    public function transform($content)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        $crawler->filter('img')->each(
            function (Crawler $node, $i) {
                $image = $node->getNode($i);
                if ($image->hasAttribute('data-src')) {
                    $src = $image->getAttribute('data-src');
                    $image->setAttribute('src', $src);
                    $image->removeAttribute('data-src');
                }
            }
        );

        return $crawler->html();
    }

    /**
     * @param mixed $content
     *
     * @return string
     */
    public function reverseTransform($content)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        // Get all img tags and parse the token.
        $crawler->filter('img')->each(
            function (Crawler $node, $i) {
                $image = $node->getNode($i);
                $src = $image->getAttribute('src');
                $parsed = parse_url($src, PHP_URL_QUERY);
                parse_str($parsed, $query);

                if ($query['token']) {
                    $image->setAttribute('src', $query['token']);
                }
                $image->setAttribute('data-src', $src);
            }
        );

        return urldecode($crawler->filter('body')->html());
    }
}