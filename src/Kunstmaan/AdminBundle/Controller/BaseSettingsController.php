<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID < 40000) {
    class_alias('Kunstmaan\AdminBundle\Controller\Symfony3BaseSettingsController', 'Kunstmaan\AdminBundle\Controller\BaseSettingsController');
} else {
    class_alias('Kunstmaan\AdminBundle\Controller\Symfony4BaseSettingsController', 'Kunstmaan\AdminBundle\Controller\BaseSettingsController');
}

if (false) {
    class BaseSettingsController extends Controller
    {
    }
}
