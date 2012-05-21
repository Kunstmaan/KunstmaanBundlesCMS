<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Symfony\Component\Form\AbstractType;


/**
 * PagePartInterface
 */
interface PagePartInterface
{

    /**
     * @return string
     */
    public function getDefaultView();

    /**
     * @return string
     */
    public function getElasticaView();

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType();

}
