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

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
            'get_social_widget_for'  => new \Twig_Function_Method($this, 'getSocialWidgetFor', array('is_safe' => array('html')))
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

        // Check if there is an SEO entity for this abstractpage.
        $seoRepo = $this->em->getRepository('KunstmaanSeoBundle:Seo');
        $seo = $seoRepo->findFor($entity);

        if (!is_null($seo)) {
            $arr[] = $seo->getMetaTitle();
        }

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

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

        // TODO: Check if it makes sense to display the widget for this platform.
        //       If the values aren't present there is no point.

        $template = 'KunstmaanSeoBundle:SeoTwigExtension:' . $platform . '_widget.html.twig';
        $template = $this->environment->loadTemplate($template);

        return $template->render($arguments);
    }

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
     * @param AbstractEntity $entity   The entity
     * @param string         $template The template
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
