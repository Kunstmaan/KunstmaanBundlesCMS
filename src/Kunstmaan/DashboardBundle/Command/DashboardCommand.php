<?php

namespace Kunstmaan\DashboardBundle\Command;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:dashboard:collect', description: 'Collect all the widget dashboard data')]
final class DashboardCommand extends Command
{
    private $widgetManager;

    public function __construct(WidgetManager $widgetManager)
    {
        parent::__construct();

        $this->widgetManager = $widgetManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var DashboardWidget[] $widgets */
        $widgets = $this->widgetManager->getWidgets();
        foreach ($widgets as $widget) {
            $command = $this->getApplication()->find($widget->getCommandName());
            $command->run(new ArrayInput([]), $output);
        }

        return 0;
    }
}
