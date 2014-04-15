<?php

namespace Kunstmaan\DashboardBundle\Widget;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

class DashboardWidget {

    /**
     * @var ContainerAwareCommand $collector
     */
    private $command;

    /**
     * @var string $controller
     */
    private $controller;

    /**
     * @param string $command
     * @param string $controller
     */
    function __construct($command, $controller)
    {
        $this->command = new $command();
        $this->controller = $controller;
    }

    /**
     * @return ContainerAwareCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    public function resolvedController(){
        $annotationReader = new AnnotationReader();
        $reflectionMethod = new \ReflectionMethod($this->controller, 'widgetAction');
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        foreach($methodAnnotations as $annotation){
            if($annotation instanceof \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route){
                if (empty($annotation)){
                    throw new \Exception("The name is not configured in the annotation");
                }
                /** @var \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route $annotation */
                return $annotation->getName();
            }
        }
        throw new \Exception("There is no route annotation");
    }
} 