<?php
/*
namespace Kunstmaan\AdminBundle\Modules;

use Symfony\Component\DependencyInjection\Container;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler
{
	private $initialized = false;
	private $pdo;
	private $container;
	private $statement;

	public function __construct(Container $container, $level = Logger::DEBUG, $bubble = true)
	{
		$this->container = $container;
		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		if (!$this->initialized) {
			$this->initialize();
		}

		$this->statement->execute(array(
				'channel' => $record['channel'],
				'level' => $record['level'],
				'message' => $record['formatted'],
				'createdat' => $record['datetime']->format('U'),
		));
	}

	private function initialize()
	{
		$this->pdo = DoctrineDoctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
		
		$this->statement = $this->pdo->prepare(
				'INSERT INTO logitem (channel, level, message, createdat) VALUES (:channel, :level, :message, :createdat)'
		);

		$this->initialized = true;
	}
}
*/
namespace Kunstmaan\AdminBundle\Modules;

use Doctrine\ORM\ORMException;

use Monolog\Handler\NullHandler;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\LogItem;
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
	        
	        /*$logitem = new ErrorLogItem();        
	        $logitem->setChannel($record['channel']);
	        $logitem->setLevel($record['level']);
	        $logitem->setMessage($record['formatted']);
	        $logitem->setCreatedAt($record['datetime']);
	
	        /*$conn = $this->container->get('doctrine')->getEntityManager()->getConnection()->getWrappedConnection();
	        $prep = $conn->prepare('INSERT INTO logitem (channel, level, message, createdat) VALUES (?, ?, ?, ?)');
	        $prep->bindParam(1, $record['channel']); 
	        $prep->bindParam(2, $record['level']);
	        $prep->bindParam(3, $record['formatted']);
	        $datetime = $record['datetime']->format('U'); 
	        $prep->bindParam(4, $datetime);
	        $prep->execute();*/
	        
	        try{
		       	/*$em = $this->container->get('doctrine')->getEntityManager();
			    $em->persist($logitem);
			    $em->flush();*/
	        	$conn = $this->container->get('doctrine')->getEntityManager()->getConnection()->getWrappedConnection();
	        	$prep = $conn->prepare('INSERT INTO errorlogitem (channel, level, message, createdat) VALUES (?, ?, ?, ?)');
	        	$prep->bindParam(1, $record['channel']);
	        	$prep->bindParam(2, $record['level']);
	        	$prep->bindParam(3, $record['formatted']);
	        	$datetime = $record['datetime']->format('U');
	        	$prep->bindParam(4, $datetime);
	        	$prep->execute();
	        }catch(\PDOException $e){
	        	
	        }
		    
		    $this->container->get('monolog.logger.doctrine')->popHandler();
	    
    }

    private function initialize(){
    	$this->initialized = true;
    }
}