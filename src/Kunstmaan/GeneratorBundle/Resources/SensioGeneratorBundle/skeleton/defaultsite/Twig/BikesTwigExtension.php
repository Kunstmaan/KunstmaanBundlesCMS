<?php

namespace {{ namespace }}\Twig;

use Doctrine\ORM\EntityManager;

class BikesTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * Constructor
     *
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
            'get_bikes' => new \Twig_Function_Method($this, 'getBikes')
        );
    }

    /**
     * @return array
     */
    public function getBikes()
    {
        return $this->em->getRepository('{{ bundle.getName() }}:Bike')->findAll();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bikes_twig_extension';
    }
}
