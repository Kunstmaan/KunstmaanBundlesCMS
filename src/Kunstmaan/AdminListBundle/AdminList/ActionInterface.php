<?php

namespace Kunstmaan\AdminListBundle\AdminList;

interface ActionInterface {

	public function getUrlFor($item);
	
	public function getLabel($item);
	
	public function getIcon($item);
	
	public function getTemplate();
	
}
