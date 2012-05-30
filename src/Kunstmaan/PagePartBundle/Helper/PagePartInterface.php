<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Symfony\Component\Form\AbstractType;


/**
 * PagePartInterface
 */
interface PagePartInterface
{

	/**
	 * Returns the view used in the frontend
	 * @abstract
	 * @return mixed
	 */
    public function getDefaultView();

	/**
	 * Returns the view used in the backend
	 * @abstract
	 * @return mixed
	 */
	public function getAdminView();

    /**
     * @return string
     */
    public function getElasticaView();

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType();

}
