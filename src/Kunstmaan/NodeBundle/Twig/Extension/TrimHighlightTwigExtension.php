<?php

namespace Kunstmaan\ViewBundle\Twig\Extension;


use Doctrine\ORM\EntityManager;

class TrimHighlightTwigExtension extends \Twig_Extension
{
	/**
     * @var \Twig_Environment
     */
    protected $environment;
    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFilters() {
        return array(
        		'trim_highlight'  => new \Twig_Filter_Method($this, 'trimhighlight')
        );
    }

    public function trimhighlight($sentence){
    	$trimmed_spaces = ltrim($sentence);
    	$trimmed = ltrim($trimmed_spaces, ".");
    	return $trimmed;
    }

    public function getName()
    {
        return 'trimhightlight_twig_extension';
    }
}
