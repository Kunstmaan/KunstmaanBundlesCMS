<?php
namespace Kunstmaan\DashboardBundle\Command;


use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DashboardCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:collect')
            ->setDescription('Collect all the widget dashboard data')
            ->addArgument(
                'configId',
                InputArgument::OPTIONAL,
                'Specify to only update one config'
            )
            ->addOption(
                'segment',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify to only update one segment',
                1
            );;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var WidgetManager $widgetManager */
        $widgetManager = $this->getContainer()->get('kunstmaan_dashboard.manager.widgets');

        /** @var DashboardWidget[] $widgets */
        $widgets = $widgetManager->getWidgets();
        foreach ($widgets as $widget) {
            /** @var DashboardWidget $widget */
            $widget->getCommand()->execute($input, $output);
        }
    }

}
