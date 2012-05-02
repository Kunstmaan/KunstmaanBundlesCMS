<?php

namespace Kunstmaan\ViewBundle\Helper;

use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

class RenderContext extends \ArrayObject
{

	private $view;

	public function getView(){
		return $this->view;
	}

	public function setView($view){
		$this->view = $view;
	}
}
