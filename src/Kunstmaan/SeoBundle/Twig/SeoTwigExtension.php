<?php

namespace Kunstmaan\SeoBundle\Twig;

use Twig_Extension;
use Twig_Environment;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

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
            'get_title_for'  => new \Twig_Function_Method($this, 'getTitleFor')
        );
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return mixed
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
        $arr = [];

        // Check if there is an SEO entity for this abstractpage.
        $seoRepo = $this->em->getRepository('KunstmaanSeoBundle:Seo');
        $seo = $seoRepo->findFor($entity);

        if (!is_null($seo)) {
            $arr[] = $seo->getMetaTitle();
        }

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
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
    public function renderSeoMetadataFor(AbstractEntity $entity, $template='KunstmaanSeoBundle:SeoTwigExtension:metadata.html.twig')
    {
        $seo = $this->getSeoFor($entity);
        $template = $this->environment->loadTemplate($template);

        return $template->render(array(
            'seo' => $seo,
            'entity' => $entity
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
