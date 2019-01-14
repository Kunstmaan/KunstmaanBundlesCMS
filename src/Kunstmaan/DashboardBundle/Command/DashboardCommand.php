<?php

namespace Kunstmaan\DashboardBundle\Command;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to collect all the widget dashboard data using bin/console kuma:dashboard:collect
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class DashboardCommand extends ContainerAwareCommand
{
    private $widgetManager;

    public function __construct(WidgetManager $widgetManager = null)
    {
        parent::__construct();

        if (!$widgetManager instanceof WidgetManager) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $widgetManager ? 'kuma:dashboard:collect' : $widgetManager);

            return;
        }

        $this->widgetManager = $widgetManager;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:collect')
            ->setDescription('Collect all the widget dashboard data');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->widgetManager) {
            $this->widgetManager = $this->getContainer()->get('kunstmaan_dashboard.manager.widgets');
        }

        /** @var DashboardWidget[] $widgets */
        $widgets = $this->widgetManager->getWidgets();
        foreach ($widgets as $widget) {
            /* @var DashboardWidget $widget */
            $widget->getCommand()->execute($input, $output);
        }
    }
}
