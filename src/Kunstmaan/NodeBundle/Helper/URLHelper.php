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

    /**
     * @var array|null
     */
    private $nodeTranslationMap = null;

    /**
     * @var array|null
     */
    private $mediaMap = null;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param EntityManager                $em
     * @param RouterInterface              $router
     * @param LoggerInterface              $logger
     * @param DomainConfigurationInterface $domainConfiguration
     */
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
     * @param $text
     *
     * @return mixed
     */
    public function replaceUrl($text)
    {
        if ($this->isEmailAddress($text)) {
            $text = sprintf('%s:%s', 'mailto', $text);
        }

        if ($this->isInternalLink($text)) {
            preg_match_all("/\[(([a-z_A-Z]+):)?NT([0-9]+)\]/", $text, $matches, PREG_SET_ORDER);

            if (count($matches) > 0) {
                $map = $this->getNodeTranslationMap();
                foreach ($matches as $match) {
                    $nodeTranslationFound = false;
                    $fullTag = $match[0];
                    $hostId = $match[2];
                    $hostConfig = $this->domainConfiguration->getFullHostById($hostId);
                    $hostBaseUrl = $this->domainConfiguration->getHostBaseUrl($hostConfig['host']);

                    $nodeTranslationId = $match[3];

                    foreach ($map as $nodeTranslation) {
                        if ($nodeTranslation['id'] == $nodeTranslationId) {
                            $urlParams = ['url' => $nodeTranslation['url']];
                            $nodeTranslationFound = true;
                            // Only add locale if multilingual site
                            if ($this->domainConfiguration->isMultiLanguage($hostConfig['host'])) {
                                $urlParams['_locale'] = $nodeTranslation['lang'];
                            }

                            // Only add other site, when having this.
                            if ($hostId) {
                                $urlParams['otherSite'] = $hostId;
                            }

                            $url = $this->router->generate('_slug', $urlParams);

                            $text = str_replace($fullTag, $hostId ? $hostBaseUrl . $url : $url, $text);
                        }
                    }

                    if (!$nodeTranslationFound) {
                        $this->logger->error('No NodeTranslation found in the database when replacing url tag ' . $fullTag);
                    }
                }
            }
        }

        if ($this->isInternalMediaLink($text)) {
            preg_match_all("/\[(([a-z_A-Z]+):)?M([0-9]+)\]/", $text, $matches, PREG_SET_ORDER);

            if (count($matches) > 0) {
                $map = $this->getMediaMap();
                foreach ($matches as $match) {
                    $mediaFound = false;
                    $fullTag = $match[0];
                    $mediaId = $match[3];

                    foreach ($map as $mediaItem) {
                        if ($mediaItem['id'] == $mediaId) {
                            $mediaFound = true;
                            $text = str_replace($fullTag, $mediaItem['url'], $text);
                        }
                    }

                    if (!$mediaFound) {
                        $this->logger->error('No Media found in the database when replacing url tag ' . $fullTag);
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Get a map of all node translations. Only called once for caching.
     *
     * @return array|null
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getNodeTranslationMap()
    {
        if (is_null($this->nodeTranslationMap)) {
            $sql = 'SELECT id, url, lang FROM kuma_node_translations';
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->execute();
            $this->nodeTranslationMap = $stmt->fetchAll();
        }

        return $this->nodeTranslationMap;
    }

    /**
     * Get a map of all media items. Only called once for caching.
     *
     * @return array|null
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getMediaMap()
    {
        if (is_null($this->mediaMap)) {
            $sql = 'SELECT id, url FROM kuma_media';
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->execute();
            $this->mediaMap = $stmt->fetchAll();
        }

        return $this->mediaMap;
    }
}
