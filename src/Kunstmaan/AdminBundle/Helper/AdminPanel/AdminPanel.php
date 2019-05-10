<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

class AdminPanel
{
    /**
     * @var AdminPanelAdaptorInterface[]
     */
    private $adaptors = array();

    /**
     * @var AdminPanelAdaptorInterface[]
     */
    private $sorted = array();

    /**
     * @var AdminPanelActionInterface[]
     */
    private $actions = null;

    /**
     * Add admin panel adaptor
     *
     * @param AdminPanelAdaptorInterface $adaptor
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
            $this->actions = array();
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
     * @return \Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAdaptorInterface[]
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
        $this->sorted = array();

        if (isset($this->adaptors)) {
            krsort($this->adaptors);
            $this->sorted = call_user_func_array('array_merge', $this->adaptors);
        }
    }
}
