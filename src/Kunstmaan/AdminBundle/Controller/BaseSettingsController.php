<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Traits\DependencyInjection\AdminListFactoryTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseSettingsController
 */
class BaseSettingsController extends AbstractController
{
    use AdminListFactoryTrait,
        TranslatorTrait;
}
