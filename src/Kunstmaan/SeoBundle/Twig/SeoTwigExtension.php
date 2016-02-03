<?php

namespace Kunstmaan\SeoBundle\Twig;

use Twig_Extension;
use Twig_Environment;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

use Kunstmaan\SeoBundle\Entity\Seo;

/**
 * Twig extensions for Seo
 */
class SeoTwigExtension extends Twig_Extension
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Website title defined in your parameters
     * @var string
     */
    private $websiteTitle;

    /**
     * Saves querying the db multiple times, if you happen to use any of the defined
     * functions more than once in your templates
     * @var array
     */
    private $seoCache = [];

    /**
     * @param EntityManager $em
     */
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
        return array(
            new \Twig_SimpleFunction('render_seo_metadata_for', array($this, 'renderSeoMetadataFor'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('get_seo_for', array($this, 'getSeoFor')),
            new \Twig_SimpleFunction('get_title_for', array($this, 'getTitleFor')),
            new \Twig_SimpleFunction('get_title_for_page_or_default', array($this, 'getTitleForPageOrDefault')),
            new \Twig_SimpleFunction('get_absolute_url', array($this, 'getAbsoluteUrl')),
            );
    }

    /**
     * Validates the $url value as URL (according to » http://www.faqs.org/rfcs/rfc2396), optionally with required components.
     * It will just return the url if it's valid. If it starts with '/', the $host will be prepended.
     *
     * @param string $url
     * @param string $host
     * @return string
     */
    public function getAbsoluteUrl($url, $host = null)
    {
        $validUrl = filter_var($url, FILTER_VALIDATE_URL);
        $host = rtrim($host, '/');

        if (!$validUrl === false) {
            // The url is valid
            return $url;
        } else {
            // Prepend with $host if $url starts with "/"
            if ($url[0] == '/' ) {
                return $url = $host.$url;
            }
            return false;
        }
    }

    /**
     * @param AbstractPage $entity
     *
     * @return Seo
     */
    public function getSeoFor(AbstractPage $entity)
    {
        $key = md5(get_class($entity).$entity->getId());

        if (!array_key_exists($key, $this->seoCache))
        {
            $seo = $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($entity);
            $this->seoCache[$key] = $seo;
        }

        return $this->seoCache[$key];
    }

    /**
     * The first value that is not null or empty will be returned.
     *
     * @param AbstractPage $entity The entity for which you want the page title.
     *
     * @return string The page title. Will look in the SEO meta first, then the NodeTranslation, then the page.
     */
    public function getTitleFor(AbstractPage $entity)
    {
        $arr = array();

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

    /**
     * @param AbstractPage $entity
     * @param null|string  $default If given we'll return this text if no SEO title was found.
     *
     * @return string
     */
    public function getTitleForPageOrDefault(AbstractPage $entity = null, $default = null)
    {
        if (is_null($entity)) {
            return $default;
        }

        $arr = array();

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $default;

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

    /**
     * @param \Twig_Environment $environment
     * @param AbstractEntity    $entity      The entity
     * @param mixed             $currentNode The current node
     * @param string            $template    The template
     *
     * @return string
     */
    public function renderSeoMetadataFor(\Twig_Environment $environment, AbstractEntity $entity, $currentNode = null, $template='KunstmaanSeoBundle:SeoTwigExtension:metadata.html.twig')
    {
        $seo = $this->getSeoFor($entity);
        $template = $environment->loadTemplate($template);

        return $template->render(array(
            'seo' => $seo,
            'entity' => $entity,
            'currentNode' => $currentNode
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_seo_twig_extension';
    }



    /**
     * @param array $values
     *
     * @return string
     */
    protected function getPreferredValue(array $values)
    {
        foreach ($values as $v) {
            if (!is_null($v) && !empty($v)) {
                return $v;
            }
        }

        return '';
    }

    /**
     * @param AbstractPage $entity
     *
     * @return null|string
     */
    private function getSeoTitle(AbstractPage $entity = null)
    {
        if (is_null($entity)) {
            return null;
        }

        $seo = $this->getSeoFor($entity);
        if (!is_null($seo)) {
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
}
