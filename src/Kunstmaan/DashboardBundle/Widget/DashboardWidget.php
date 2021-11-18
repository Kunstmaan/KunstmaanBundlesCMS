<?php

namespace Kunstmaan\DashboardBundle\Widget;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardWidget
{
    /** @var string */
    private $commandName;

    /**
     * @var ContainerAwareCommand
     */
    private $command;

    /**
     * @var string
     */
    private $controller;

    /**
     * @param string $command
     * @param string $controller
     */
    public function __construct($command, $controller, ContainerInterface $container = null)
    {
        $this->controller = $controller;

        if ($container instanceof ContainerInterface) {
            @trigger_error(sprintf('The "$container" argument of "%s" is deprecated since KunstmaanDashboardBundle 5.9 and will be removed in KunstmaanDashboardBundle 6.0.', __METHOD__), E_USER_DEPRECATED);
        }

        if (class_exists($command, false)) {
            @trigger_error(sprintf('Passing a command classname for the "$command" argument in "%s" is deprecated since KunstmaanDashboardBundle 5.9 and will not be allowed in KunstmaanDashboardBundle 6.0. Pass a command name instead.', __METHOD__), E_USER_DEPRECATED);

            $this->command = new $command();
            $this->command->setContainer($container);

            return;
        }

        $this->commandName = $command;
    }

    /**
     * @deprecated since KunstmaanDashboardBundle 5.9. Use `getCommandName` instead.
     *
     * @return ContainerAwareCommand
     */
    public function getCommand()
    {
        return $this->command;
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
