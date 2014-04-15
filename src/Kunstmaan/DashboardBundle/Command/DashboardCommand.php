<?php
namespace Kunstmaan\DashboardBundle\Command;


use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WidgetDataCollectorCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:collect')
            ->setDescription('Collect all the widget dashboard data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var WidgetManager $widgetManager */
        $widgetManager = $this->get('kunstmaan_dashboard.manager.widgets');
        /** @var DashboardWidget[] $widgets */
        $widgets = $widgetManager->getWidgets();
        foreach($widgets as $widget){
            /** @var DashboardWidget $widget */
            $widget->getCommand()->execute($input, $output);
        }
    }

} 