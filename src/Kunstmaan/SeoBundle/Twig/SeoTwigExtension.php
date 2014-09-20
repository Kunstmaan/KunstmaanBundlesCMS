<?php

namespace Kunstmaan\SeoBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\SeoBundle\Entity\Seo;
use Twig_Environment;
use Twig_Extension;

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
            'render_seo_metadata_for'  => new \Twig_Function_Method($this, 'renderSeoMetadataFor', array('is_safe' => array('html'), 'needs_environment' => true)),
            'get_seo_for'  => new \Twig_Function_Method($this, 'getSeoFor'),
            'get_title_for'  => new \Twig_Function_Method($this, 'getTitleFor'),
            'get_title_for_page_or_default' => new \Twig_Function_Method($this, 'getTitleForPageOrDefault'),
            'get_social_widget_for'  => new \Twig_Function_Method($this, 'getSocialWidgetFor', array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    /**
     * @param AbstractPage $entity
     *
     * @return Seo
     */
    public function getSeoFor(AbstractPage $entity)
    {
        return $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($entity);
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
     * @param AbstractPage      $entity      The page
     * @param string            $platform    The platform like facebook or linkedin.
     *
     * @throws \InvalidArgumentException
     * @return boolean|string
     */
    public function getSocialWidgetFor(\Twig_Environment $environment, AbstractPage $entity, $platform)
    {
        $seo = $this->getSeoFor($entity);

        if (is_null($seo)) {
            return false;
        }

        $arguments = array();
        if ($platform == 'linkedin') {
            $arguments = array(
                'productid' => $seo->getLinkedInRecommendProductID(),
                'url' => $seo->getLinkedInRecommendLink()
            );

            if (empty($arguments['url'])) {
                $arguments['url'] = $seo->getOgUrl();
            }
        } elseif ($platform == 'facebook') {
            $arguments = array(
                'url' => $seo->getOgUrl()
            );
        } else {
            throw new \InvalidArgumentException('Only linkedin and facebook are supported for now.');
        }

        // If not a single argument is present we can be sure the button will be useless.
        // This is just a catchall. For more specific behaviour you can return false sooner in the platform specific check.
        if (!array_filter($arguments)) {
            return false;
        }

        $template = 'KunstmaanSeoBundle:SeoTwigExtension:' . $platform . '_widget.html.twig';
        $template = $environment->loadTemplate($template);

        return $template->render($arguments);
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
