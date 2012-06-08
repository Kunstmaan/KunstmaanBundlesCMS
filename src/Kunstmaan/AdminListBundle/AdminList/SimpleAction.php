<?php
namespace Kunstmaan\AdminListBundle\AdminList;
class SimpleAction implements ActionInterface {
	
	private $url;
	private $icon;
	private $label;
	private $template;
	
	public function __construct($url, $icon, $label, $template = null) {
		$this->url = $url;
		$this->icon = $icon;
		$this->label = $label;
		$this->template = $template;
	}
	
	function getUrlFor($item)
	{
		return $this->$url;
	}
	
	function getIcon($item)
	{
		return $this->icon;
	}
	
	function getLabel($item)
	{
		return $this->label;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}

}
