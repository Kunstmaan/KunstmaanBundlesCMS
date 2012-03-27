<?php

namespace Kunstmaan\AdminBundle\Modules;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\ErrorLogItem;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $container;

    public function __construct(Container &$container, $level = Logger::ERROR, $bubble = true){
        $this->container = $container;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record){
        if (!$this->initialized) {
            $this->initialize();
        }

        $this->container->get('monolog.logger.doctrine')->pushHandler(new NullHandler());

	    try{
	    	$token = $this->container->get('security.context')->getToken();
	    	$user=null;
	    	if(isset($token)){
	    		$user = $token->getUser();
	    	}

	    	$logitem = new ErrorLogItem();
	    	if($user instanceof User){
	    		$logitem->setUser($user);
	    	}
	    	$logitem->setStatus("error");
	        $logitem->setChannel($record['channel']);
	        $logitem->setLevel($record['level']);
	        $logitem->setMessage($record['formatted']);
		    $em = $this->container->get('doctrine')->getEntityManager();
		    if($em->isOpen()){
		    	$em->persist($logitem);
		    	$em->flush();
		    }
	     }catch(\PDOException $e){
	     	// catching the exception during fullreload: errorlogitem table not found
	     	// TODO do something useful
	     }catch(\ORMException $e){
	     	// catching the exception during fullreload: The EntityManager is closed
	     	// TODO do something useful
	     }

		 $this->container->get('monolog.logger.doctrine')->popHandler();

    }

    private function initialize(){
    	$this->initialized = true;
    }
}