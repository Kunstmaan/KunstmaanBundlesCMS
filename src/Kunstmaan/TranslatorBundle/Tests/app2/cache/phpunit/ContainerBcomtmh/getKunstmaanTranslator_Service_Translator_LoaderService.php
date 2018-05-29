<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'kunstmaan_translator.service.translator.loader' shared service.

$this->services['kunstmaan_translator.service.translator.loader'] = $instance = new \Kunstmaan\TranslatorBundle\Service\Translator\Loader();

$instance->setTranslationRepository(${($_ = isset($this->services['kunstmaan_translator.repository.translation']) ? $this->services['kunstmaan_translator.repository.translation'] : $this->load(__DIR__.'/getKunstmaanTranslator_Repository_TranslationService.php')) && false ?: '_'});

return $instance;
