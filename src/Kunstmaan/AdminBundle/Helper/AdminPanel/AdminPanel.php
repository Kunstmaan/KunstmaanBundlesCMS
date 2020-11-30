<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

class AdminPanel
{
    /**
     * @var AdminPanelAdaptorInterface[]
     */
    private $adaptors = [];

    /**
     * @var AdminPanelAdaptorInterface[]
     */
    private $sorted = [];

    /**
     * @var AdminPanelActionInterface[]
     */
    private $actions = null;

    /**
     * Add admin panel adaptor
     */
    public function addAdminPanelAdaptor(AdminPanelAdaptorInterface $adaptor, $priority = 0)
    {
        $this->adaptors[$priority][] = $adaptor;
        unset($this->sorted);
    }

    /**
     * Return current admin panel actions
     */
    public function getAdminPanelActions()
    {
        if (!$this->actions) {
            $this->actions = [];
            $adaptors = $this->getAdaptors();
            foreach ($adaptors as $adaptor) {
                $this->actions = array_merge($this->actions, $adaptor->getAdminPanelActions());
            }
        }

        return $this->actions;
    }

    /**
     * Get adaptors sorted by priority.
     *
     * @return AdminPanelAdaptorInterface[]
     */
    private function getAdaptors()
    {
        if (!isset($this->sorted)) {
            $this->sortAdaptors();
        }

        return $this->sorted;
    }

    /**
     * Sorts the internal list of adaptors by priority.
     */
    private function sortAdaptors()
    {
        $this->sorted = [];

        if (isset($this->adaptors)) {
            krsort($this->adaptors);
            $this->sorted = array_merge(...$this->adaptors);
        }
    }
}
