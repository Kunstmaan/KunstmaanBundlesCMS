<?php
namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * @author kristof
 */
class Field {

	private $fieldheader;
    private $fieldname;
    private $sort;
    
	public function __construct($fieldname, $fieldheader, $sort){
		$this->fieldname = $fieldname;
		$this->fieldheader = $fieldheader;
		$this->sort = $sort;
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
}
