<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(false)
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => false,
        'phpdoc_summary' => false,
        'class_attributes_separation' => ['elements' => ['method', 'property']],
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
    ;
