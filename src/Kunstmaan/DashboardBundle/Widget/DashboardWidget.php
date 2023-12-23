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

        // NEXT_MAJOR Remove annotation support
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($methodAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                if (null === $annotation->getName()) {
                    throw new \Exception('The name is not configured in the annotation');
                }

                return $annotation->getName();
            }
        }

        $methodRouteAttributes = $reflectionMethod->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);
        if ($methodRouteAttributes === []) {
            throw new \Exception('There is no route annotation or attribute');
        }

        $attributeInstance = $methodRouteAttributes[0]->newInstance();
        if (null === $attributeInstance->getName()) {
            throw new \Exception('The name is not configured in the attribute');
        }

        return $attributeInstance->getName();
    }
}
