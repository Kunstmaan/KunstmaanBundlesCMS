<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * omnext logitem
 * 
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\ErrorLogItemRepository")
 * @ORM\Table(name="errorlogitem")
 */
class ErrorLogItem{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $channel;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $level;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $message;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdat;

    public function __construct(){
     	$this->createdat = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @param id integer
     */
    public function setId($id){
    	$this->id = $id;
    }

    public function getChannel(){
    	return $this->channel;
    }
    
    public function setChannel($channel){
    	$this->channel = $channel;
    }
    
    public function getLevel(){
    	return $this->level;
    }
    
    public function setLevel($level){
    	$this->level = $level;
    }
    
    public function getMessage(){
    	return $this->message;
    }
    
    public function setMessage($message){
    	$this->message = $message;
    }
    
    public function getCreatedAt(){
    	return $this->createdat;
    }
    
    public function setCreatedAt($createdat){
    	$this->createdat = $createdat;
    }
}