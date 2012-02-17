<?php

namespace Kunstmaan\AdminBundle\Modules;

use Doctrine\ORM\ORMException;

use Monolog\Handler\NullHandler;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\ErrorLogItem;
use Monolog\Logger;
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
	    	$logitem = new ErrorLogItem();
	        $logitem->setChannel($record['channel']);
	        $logitem->setLevel($record['level']);
	        $logitem->setMessage($record['formatted']);
		    $em = $this->container->get('doctrine')->getEntityManager();
			$em->persist($logitem);
			$em->flush();
	        /*$conn = $this->container->get('doctrine')->getEntityManager()->getConnection()->getWrappedConnection();
	        $prep = $conn->prepare('INSERT INTO errorlogitem (channel, level, message) VALUES (?, ?, ?)');
	        $prep->bindParam(1, $record['channel']);
	        $prep->bindParam(2, $record['level']);
	        $prep->bindParam(3, $record['formatted']);
	        $prep->execute();*/
	     }catch(\PDOException $e){
	     	// catching the exception during fullreload: errorlogitem table not found
	     }catch(\ORMException $e){
	     	// catching the exception during fullreload: EntityManager not set
	     }
		    
		 $this->container->get('monolog.logger.doctrine')->popHandler();
	    
    }

    private function initialize(){
    	$this->initialized = true;
    }
}