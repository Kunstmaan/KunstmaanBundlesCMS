<?php

namespace Kunstmaan\DashboardBundle\Widget;


use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashboardWidget
{

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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    function __construct($command, $controller, ContainerInterface $container)
    {
        $this->command = new $command();
        $this->command->setContainer($container);
        $this->controller = $controller;
    }

    /**
     * @return ContainerAwareCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    public function resolvedController()
    {
        $annotationReader = new AnnotationReader();
        $reflectionMethod = new \ReflectionMethod($this->controller, 'widgetAction');
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($methodAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                if (empty($annotation)) {
                    throw new \Exception("The name is not configured in the annotation");
                }
                /** @var \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route $annotation */
                return $annotation->getName();
            }
        }
        throw new \Exception("There is no route annotation");
    }
}
