<?php

namespace Kunstmaan\SeoBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\SeoBundle\Entity\Seo;
use Psr\Cache\CacheItemPoolInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extensions for Seo
 *
 * @final since 5.4
 */
class SeoTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Website title defined in your parameters
     *
     * @var string
     */
    private $websiteTitle;

    /**
     * Saves querying the db multiple times, if you happen to use any of the defined
     * functions more than once in your templates
     *
     * @var array
     */
    private $seoCache = [];

    /**
     * @var CacheItemPoolInterface
     */
    private $requestCache;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render_seo_metadata_for', [$this, 'renderSeoMetadataFor'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('get_seo_for', [$this, 'getSeoFor']),
            new TwigFunction('get_title_for', [$this, 'getTitleFor']),
            new TwigFunction('get_title_for_page_or_default', [$this, 'getTitleForPageOrDefault']),
            new TwigFunction('get_absolute_url', [$this, 'getAbsoluteUrl']),
            new TwigFunction('get_image_dimensions', [$this, 'getImageDimensions']),
        ];
    }

    /**
     * Validates the $url value as URL (according to » http://www.faqs.org/rfcs/rfc2396), optionally with required components.
     * It will just return the url if it's valid. If it starts with '/', the $host will be prepended.
     *
     * @param string $url
     * @param string $host
     *
     * @return string
     */
    public function getAbsoluteUrl($url, $host = null)
    {
        $validUrl = filter_var($url, FILTER_VALIDATE_URL);
        $host = rtrim($host, '/');

        if (!$validUrl === false) {
            // The url is valid
            return $url;
        }

        // Prepend with $host if $url starts with "/"
        if (strpos($url, '/') === 0) {
            return $url = $host . $url;
        }

        return false;
    }

    /**
     * @return Seo
     */
    public function getSeoFor(AbstractPage $entity)
    {
        $key = md5(\get_class($entity) . $entity->getId());

        if (!\array_key_exists($key, $this->seoCache)) {
            $seo = $this->em->getRepository(Seo::class)->findOrCreateFor($entity);
            $this->seoCache[$key] = $seo;
        }

        return $this->seoCache[$key];
    }

    /**
     * The first value that is not null or empty will be returned.
     *
     * @param AbstractPage $entity the entity for which you want the page title
     *
     * @return string The page title. Will look in the SEO meta first, then the NodeTranslation, then the page.
     */
    public function getTitleFor(AbstractPage $entity)
    {
        $arr = [];

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

    /**
     * @param AbstractPage $entity
     * @param string|null  $default if given we'll return this text if no SEO title was found
     *
     * @return string
     */
    public function getTitleForPageOrDefault(AbstractPage $entity = null, $default = null)
    {
        if (\is_null($entity)) {
            return $default;
        }

        $arr = [];

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $default;

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

    /**
     * @param AbstractEntity $entity      The entity
     * @param mixed          $currentNode The current node
     * @param string         $template    The template
     *
     * @return string
     */
    public function renderSeoMetadataFor(Environment $environment, AbstractEntity $entity, $currentNode = null, $template = '@KunstmaanSeo/SeoTwigExtension/metadata.html.twig')
    {
        $seo = $this->getSeoFor($entity);
        $template = $environment->load($template);

        return $template->render(
            [
                'seo' => $seo,
                'entity' => $entity,
                'currentNode' => $currentNode,
            ]
        );
    }

    /**
     * @return string
     */
    protected function getPreferredValue(array $values)
    {
        foreach ($values as $v) {
            if (!\is_null($v) && !empty($v)) {
                return $v;
            }
        }

        return '';
    }

    /**
     * @param AbstractPage $entity
     *
     * @return string|null
     */
    private function getSeoTitle(AbstractPage $entity = null)
    {
        if (\is_null($entity)) {
            return null;
        }

        $seo = $this->getSeoFor($entity);
        if (!\is_null($seo)) {
            $title = $seo->getMetaTitle();
            if (!empty($title)) {
                return str_replace('%websitetitle%', $this->getWebsiteTitle(), $title);
            }
        }

        return null;
    }

    /**
     * Gets the Website title defined in your parameters.
     *
     * @return string
     */
    public function getWebsiteTitle()
    {
        return $this->websiteTitle;
    }

    /**
     * Sets the Website title defined in your parameters.
     *
     * @param string $websiteTitle the website title
     *
     * @return self
     */
    public function setWebsiteTitle($websiteTitle)
    {
        $this->websiteTitle = $websiteTitle;

        return $this;
    }

    /**
     * @param $src
     *
     * @return array
     */
    public function getImageDimensions($src)
    {
        list($width, $height) = $this->getImageSize($src);

        return ['width' => $width, 'height' => $height];
    }

    public function setRequestCache(CacheItemPoolInterface $cacheService)
    {
        $this->requestCache = $cacheService;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getRequestCache()
    {
        return $this->requestCache;
    }

    private function getImageSize($src)
    {
        try {
            $cache = $this->getRequestCache();
            if (null === $cache) {
                return getimagesize($src);
            }

            $cachedImageSizes = $cache->getItem(md5($src));
            if (!$cachedImageSizes->isHit()) {
                $sizes = getimagesize($src);

                $cachedImageSizes->set($sizes);
                $cache->save($cachedImageSizes);
            }

            return $cachedImageSizes->get();
        } catch (\Exception $e) {
            return [null, null];
        }
    }
}
