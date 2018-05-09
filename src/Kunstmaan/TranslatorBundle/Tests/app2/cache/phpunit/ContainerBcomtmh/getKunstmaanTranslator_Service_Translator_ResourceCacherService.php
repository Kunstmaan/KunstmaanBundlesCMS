<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'kunstmaan_translator.service.translator.resource_cacher' shared service.

$this->services['kunstmaan_translator.service.translator.resource_cacher'] = $instance = new \Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher();

$instance->setDebug(true);
$instance->setCacheDir(($this->targetDirs[0].'/translations'));
$instance->setLogger(${($_ = isset($this->services['logger']) ? $this->services['logger'] : $this->load(__DIR__.'/getLoggerService.php')) && false ?: '_'});

return $instance;
