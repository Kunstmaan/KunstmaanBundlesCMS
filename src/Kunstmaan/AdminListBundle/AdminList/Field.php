<?php
namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * @author kristof
 */
class Field {

	private $fieldheader;
    private $fieldname;
    private $sort;
    private $template;
    
	public function __construct($fieldname, $fieldheader, $sort = false, $template = null){
		$this->fieldname = $fieldname;
		$this->fieldheader = $fieldheader;
		$this->sort = $sort;
		$this->template = $template;
	}
	
	public function getFieldname(){
		return $this->fieldname;
	}
	
	public function getFieldheader(){
		return $this->fieldheader;
	}
	
	public function isSortable(){
		return $this->sort;
	}
	
	public function getTemplate(){
	    return $this->template;
	}
}
