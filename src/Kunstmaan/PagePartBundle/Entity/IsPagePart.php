<?php

namespace  Kunstmaan\PagePartBundle\Entity;

/**
 * Interface that has to be implemented by all pageparts
 *
 * @author Kristof Van Cauwenbergh
 */
interface IsPagePart {
	
	public function getDefaultView();
	
	public function getElasticaView();
	
	public function getDefaultAdminType();
	
}
