<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\KernelInterface;

use Kunstmaan\AdminBundle\Event\ApplyAclChangesetEvent;
use Kunstmaan\NodeBundle\Helper\ShellHelper;

/**
 * ApplyAclChangesetListener
 */
class ApplyAclChangesetListener
{
    /**
     * @var ShellHelper $shellHelper
     */
    protected $shellHelper;

    /**
     * @var KernelInterface $kernel
     */
    protected $kernel;

    /**
     * @param ShellHelper     $shellHelper The shell helper
     * @param KernelInterface $kernel      The kernel
     */
    public function __construct(ShellHelper $shellHelper, KernelInterface $kernel)
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
