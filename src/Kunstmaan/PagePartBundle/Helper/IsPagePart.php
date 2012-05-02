<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

interface IsPagePart{

	public function getDefaultView();
	
	public function getElasticaView();
	
	public function getDefaultAdminType();
	
}