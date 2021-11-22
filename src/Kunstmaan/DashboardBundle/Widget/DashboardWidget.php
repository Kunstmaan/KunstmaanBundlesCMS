<?php

namespace Kunstmaan\DashboardBundle\Widget;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\Annotation\Route;

class DashboardWidget
{
    /** @var string */
    private $commandName;

    /** @var string */
    private $controller;

    public function __construct(string $commandName, string $controller)
    {
        $this->controller = $controller;
        $this->commandName = $commandName;
    }

    public function getCommandName()
    {
        return $this->commandName;
    }

    public function resolvedController()
    {
        $annotationReader = new AnnotationReader();
        $reflectionMethod = new \ReflectionMethod($this->controller, 'widgetAction');
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($methodAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                if (null === $annotation->getName()) {
                    throw new \Exception('The name is not configured in the annotation');
                }

                return $annotation->getName();
            }
        }

        throw new \Exception('There is no route annotation');
    }
}
