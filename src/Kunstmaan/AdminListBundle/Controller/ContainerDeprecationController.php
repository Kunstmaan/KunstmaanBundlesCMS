<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID < 40000) {
    class_alias('Kunstmaan\AdminListBundle\Controller\Symfony3ContainerDeprecationController', 'Kunstmaan\AdminListBundle\Controller\ContainerDeprecationController');
} else {
    class_alias('Kunstmaan\AdminListBundle\Controller\Symfony4ContainerDeprecationController', 'Kunstmaan\AdminListBundle\Controller\ContainerDeprecationController');
}
if (false) {
    class ContainerDeprecationController extends Controller
    {
    }
}
