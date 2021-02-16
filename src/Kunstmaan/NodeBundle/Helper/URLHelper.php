<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Validation\URLValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * A helper for replacing url's
 */
class URLHelper
{
    use URLValidator;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var array */
    private $nodeTranslationCache = [];

    /** @var array */
    private $mediaCache = [];

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    public function __construct(EntityManager $em, RouterInterface $router, LoggerInterface $logger, DomainConfigurationInterface $domainConfiguration)
    {
        $this->em = $em;
        $this->router = $router;
        $this->logger = $logger;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * Replace a given text, according to the node translation id and the multidomain site id.
     *
     * @param string $text
     *
     * @return string
     */
    public function replaceUrl($text)
    {
        if ($this->isEmailAddress($text)) {
            $text = sprintf('%s:%s', 'mailto', $text);
        }

        if ($this->isInternalLink($text)) {
            preg_match_all("/\[(([a-z_A-Z\.]+):)?NT([0-9]+)\]/", $text, $matches, PREG_SET_ORDER);

            if (\count($matches) > 0) {
                foreach ($matches as $match) {
                    $fullTag = $match[0];
                    $hostId = $match[2];

                    $hostConfig = !empty($hostId) ? $this->domainConfiguration->getFullHostById($hostId) : null;
                    $host = null !== $hostConfig && array_key_exists('host', $hostConfig) ? $hostConfig['host'] : null;
                    $hostBaseUrl = $this->domainConfiguration->getHostBaseUrl($host);

                    $nodeTranslationId = $match[3];
                    $nodeTranslation = $this->getNodeTranslation($nodeTranslationId);

                    if ($nodeTranslation) {
                        $urlParams = ['url' => $nodeTranslation['url']];
                        // Only add locale if multilingual site
                        if ($this->domainConfiguration->isMultiLanguage($host)) {
                            $urlParams['_locale'] = $nodeTranslation['lang'];
                        }

                        // Only add other site, when having this.
                        if ($hostId) {
                            $urlParams['otherSite'] = $hostId;
                        }

                        $url = $this->router->generate('_slug', $urlParams);

                        $text = str_replace($fullTag, $hostId ? $hostBaseUrl . $url : $url, $text);
                    } else {
                        $this->logger->error('No NodeTranslation found in the database when replacing url tag ' . $fullTag);
                    }
                }
            }
        }

        if ($this->isInternalMediaLink($text)) {
            preg_match_all("/\[(([a-z_A-Z]+):)?M([0-9]+)\]/", $text, $matches, PREG_SET_ORDER);

            if (\count($matches) > 0) {
                foreach ($matches as $match) {
                    $fullTag = $match[0];
                    $mediaId = $match[3];

                    $mediaItem = $this->getMedia($mediaId);
                    if ($mediaItem) {
                        $text = str_replace($fullTag, $mediaItem['url'], $text);
                    } else {
                        $this->logger->error('No Media found in the database when replacing url tag ' . $fullTag);
                    }
                }
            }
        }

        return $text;
    }

    private function getNodeTranslation($nodeTranslationId): array
    {
        if (isset($this->nodeTranslationCache[$nodeTranslationId])) {
            return $this->nodeTranslationCache[$nodeTranslationId];
        }

        $stmt = $this->em->getConnection()->executeQuery(
            'SELECT url, lang FROM kuma_node_translations WHERE id = :nodeTranslationId',
            ['nodeTranslationId' => $nodeTranslationId]
        );
        $nodeTranslation = $stmt->fetch();

        $this->nodeTranslationCache[$nodeTranslationId] = $nodeTranslation;

        return $nodeTranslation;
    }

    private function getMedia($mediaId): array
    {
        if (isset($this->mediaCache[$mediaId])) {
            return $this->mediaCache[$mediaId];
        }

        $stmt = $this->em->getConnection()->executeQuery(
            'SELECT url FROM kuma_media WHERE id = :mediaId',
            ['mediaId' => $mediaId]
        );
        $media = $stmt->fetch();

        $this->mediaCache[$mediaId] = $media;

        return $media;
    }
}
