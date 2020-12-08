<?php

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\DataTransformerInterface;

class MediaTokenTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $content
     *
     * @return string
     */
    public function transform($content)
    {
        if (!trim($content)) {
            return '';
        }

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        $crawler->filter('img,a')->each(
            function (Crawler $node) {
                $element = $node->getNode(0);
                $attribute = $element->nodeName === 'img' ? 'src' : 'href';
                if ($element->hasAttribute('data-' . $attribute)) {
                    $attributeValue = $element->getAttribute('data-' . $attribute);
                    $element->setAttribute($attribute, $attributeValue);
                    $element->removeAttribute('data-' . $attribute);
                }
            }
        );

        try {
            return $crawler->html();
        } catch (\InvalidArgumentException $exception) {
            return $content;
        }
    }

    /**
     * @param mixed $content
     *
     * @return string
     */
    public function reverseTransform($content)
    {
        if (!trim($content)) {
            return '';
        }

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        // Get all img and a tags and parse the token.
        $crawler->filter('img,a')->each(
            function (Crawler $node) {
                $element = $node->getNode(0);
                $attribute = $element->nodeName === 'img' ? 'src' : 'href';
                $attributeValue = $element->getAttribute($attribute);
                $parsed = parse_url($attributeValue, PHP_URL_QUERY);
                parse_str($parsed, $query);

                if (isset($query['token'])) {
                    $element->setAttribute($attribute, $query['token']);
                }
                $element->setAttribute('data-' . $attribute, $attributeValue);
            }
        );

        try {
            $html = $crawler->filter('body')->html();

            // URL-decode square brackets in img and a tags
            $html = preg_replace_callback(
                '/<(img|a)\s+[^>]*>/',
                function ($matches) {
                    return str_replace(['%5B', '%5D'], ['[', ']'], $matches[0]);
                },
                $html
            );

            return $html;
        } catch (\InvalidArgumentException $exception) {
            return $content;
        }
    }
}
