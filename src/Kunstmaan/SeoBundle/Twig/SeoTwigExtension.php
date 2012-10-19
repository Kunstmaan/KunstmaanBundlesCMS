<?php

namespace Kunstmaan\SeoBundle\Twig;

use Twig_Extension;
use Twig_Environment;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

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
            'get_seo_for'  => new \Twig_Function_Method($this, 'getSeoFor')
        );
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return mixed
     */
    public function getSeoFor(AbstractEntity $entity)
    {
        return $this->em->getRepository('KunstmaanSeoBundle:Seo')->findOrCreateFor($entity);
    }

    /**
     * @param AbstractEntity $entity
     * @param string $template
     *
     * @return string
     */
    public function renderSeoMetadataFor(AbstractEntity $entity, $template='KunstmaanSeoBundle:SeoTwigExtension:metadata.html.twig')
    {
        $seo = $this->getSeoFor($entity);
        $template = $this->environment->loadTemplate($template);

        return $template->render(array(
            'seo' => $seo
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
