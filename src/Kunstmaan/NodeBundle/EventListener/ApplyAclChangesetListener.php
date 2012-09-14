<?php

namespace Kunstmaan\AdminNodeBundle\EventListener;

use Symfony\Component\HttpKernel\KernelInterface;

use Kunstmaan\AdminBundle\Helper\Event\ApplyAclChangesetEvent;
use Kunstmaan\AdminNodeBundle\Helper\ShellHelper;

class ApplyAclChangesetListener
{
    /* @var ShellHelper $shellHelper */
    protected $shellHelper;

    /* @var KernelInterface $kernel */
    protected $kernel;

    /**
     * @param \Kunstmaan\AdminNodeBundle\Helper\ShellHelper $shellHelper
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    public function __construct(ShellHelper $shellHelper, KernelInterface $kernel)
    {
        $this->shellHelper = $shellHelper;
        $this->kernel = $kernel;
    }

    public function onApplyAclChangeset(ApplyAclChangesetEvent $event)
    {
        // Launch acl command
        $cmd = 'php ' . $this->kernel->getRootDir() . '/console kuma:acl:apply';
        $cmd .= ' --env=' . $this->kernel->getEnvironment();

        $this->shellHelper->runInBackground($cmd);
    }

}
