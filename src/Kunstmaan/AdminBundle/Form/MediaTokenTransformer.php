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
        $html = utf8_encode("<!DOCTYPE html>
        <html>
            <body>
                ".$content."
            </body>
        </html>");

        $crawler = new Crawler($html);

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
        // All on one line because of HTML parsing and empty lines.
        $html = utf8_encode("<!DOCTYPE html><html><body>".$content."</body></html>");

        $crawler = new Crawler($html);

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