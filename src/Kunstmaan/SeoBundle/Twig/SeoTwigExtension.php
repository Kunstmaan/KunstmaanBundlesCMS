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
     * @var Twig_Environment
     */
    protected $environment;


    protected $accountId;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $accountId = null)
    {
        $this->em = $em;
        $this->accountId = $accountId;
    }


    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'render_seo_metadata_for'  => new \Twig_Function_Method($this, 'renderSeoMetadataFor', array('is_safe' => array('html'))),
            'get_seo_for'  => new \Twig_Function_Method($this, 'getSeoFor'),
            'get_title_for'  => new \Twig_Function_Method($this, 'getTitleFor'),
            'get_title_for_page_or_default' => new \Twig_Function_Method($this, 'getTitleForPageOrDefault'),
            'get_social_widget_for'  => new \Twig_Function_Method($this, 'getSocialWidgetFor', array('is_safe' => array('html'))),
            'initialize_google_analytics' => new \Twig_Function_Method($this, 'initializeGoogleAnalytics', array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function getSeoFor(AbstractPage $entity)
    {
        return $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($entity);
    }

    protected $accountVarName = 'account_id';

    /**
     * Renders the default Google Analytics JavaScript.
     *
     * If the options are not set it'll try and load the account ID from your parameters (google.analytics.account_id)
     *
     * @param Twig_Environment $environment
     * @param array|null $options {account_id: 'UA-XXXXX-Y'}
     */
    public function initializeGoogleAnalytics(\Twig_Environment $environment, $options = null)
    {
        if (is_null($options)) {
            $options = array();
        }

        $defaults = array();

        $this->setOptionIfNotSet($defaults, $this->accountVarName, $this->accountId);
        // $this->setOptionIfNotSet($defaults, $this->accountVarName, $this->getGlobal($environment, 'ga_code')); // Global logic not working.

        // Things set in $options will override things set in $defaults.
        $options = array_merge($defaults, $options);

        if (!$this->isOptionSet($options, $this->accountVarName)) {
            throw new \Twig_Error_Runtime("The KunstmaanSeoBundle depends on a Google Analytics account ID. You can either pass this along in the initialize_google_analytics function ($this->accountVarName), provide a variable under 'parameters.google.analytics.account_id'.");
        }

        $template = $environment->loadTemplate('KunstmaanSeoBundle:SeoTwigExtension:google_analytics_init.html.twig');
        return $template->render($options);

    }

    /**
     * Prefer the given
     * @param Twig_Environment $environment
     */
    private function setOptionIfNotSet(&$arr, $option, $value) {
        if ($this->isOptionSet($arr, $option)) {
            $arr[$option] = $value;
        }
    }

    private function isOptionSet($arr, $option) {
        return (!isset($arr[$option]) || !empty($arr[$option]));
    }

    /**
     * Not sure if this works ... doesn't appear to see all the globals.
     *
     * @param Twig_Environment $environment
     * @param $name
     * @return null
     */
    private function getGlobal(\Twig_Environment $environment, $name) {
        foreach ($environment->getGlobals() as $k => $v) {
            if ($k == $name) {
                return $v;
            }
        }

        return null;
    }

    /**
     * The first value that is not null or empty will be returned.
     *
     * @param AbstractEntity $entity The entity for which you want the page title.
     *
     * @return The page title. Will look in the SEO meta first, then the NodeTranslation, then the page.
     */
    public function getTitleFor(AbstractPage $entity)
    {
        $arr = array();

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }


    private function getSeoTitle(AbstractPage $entity = null)
    {
        if (is_null($entity)) {
            return null;
        }

        $seo = $this->getSeoFor($entity);

        if (!is_null($seo)) {
            $title = $seo->getMetaTitle();
            if (!empty($title)) {
                return $title;
            }
        }

        return null;
    }


    /**
     *
     * @param AbstractPage $entity
     * @param null $default If given we'll return this text if no SEO title was found.
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
     * @param AbstractPage $entity   The page
     * @param string       $platform The platform
     *
     * @throws \InvalidArgumentException
     * @return boolean|string
     */
    public function getSocialWidgetFor(AbstractPage $entity, $platform)
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
        $template = $this->environment->loadTemplate($template);

        return $template->render($arguments);
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
     * @param AbstractEntity $entity      The entity
     * @param object         $currentNode The current node
     * @param string         $template    The template
     *
     * @return string
     */
    public function renderSeoMetadataFor(AbstractEntity $entity, $currentNode = null, $template='KunstmaanSeoBundle:SeoTwigExtension:metadata.html.twig')
    {
        $seo = $this->getSeoFor($entity);
        $template = $this->environment->loadTemplate($template);

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

}
