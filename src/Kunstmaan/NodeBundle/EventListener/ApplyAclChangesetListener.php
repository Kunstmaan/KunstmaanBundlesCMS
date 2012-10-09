<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\KernelInterface;

use Kunstmaan\AdminBundle\Event\ApplyAclChangesetEvent;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;

/**
 * ApplyAclChangesetListener
 */
class ApplyAclChangesetListener
{
    /**
     * @var Shell
     */
    protected $shellHelper;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param Shell           $shellHelper The shell helper
     * @param KernelInterface $kernel      The kernel
     */
    public function __construct(Shell $shellHelper, KernelInterface $kernel)
    {
        $this->shellHelper = $shellHelper;
        $this->kernel = $kernel;
    }

    /**
     * @param ApplyAclChangesetEvent $event
     */
    public function onApplyAclChangeset(/** @noinspection PhpUnusedParameterInspection */ApplyAclChangesetEvent $event)
    {
        // Launch acl command
        $cmd = 'php ' . $this->kernel->getRootDir() . '/console kuma:acl:apply';
        $cmd .= ' --env=' . $this->kernel->getEnvironment();

        $this->shellHelper->runInBackground($cmd);
    }

}
