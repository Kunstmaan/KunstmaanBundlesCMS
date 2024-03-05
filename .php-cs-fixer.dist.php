<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle',
        'src/Kunstmaan/CookieBundle/Resources/skeleton',
        'node_modules'
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@Symfony' => true,
        'class_attributes_separation' =>  ['elements' => ['method' => 'one']],
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_summary' => false,
        'yoda_style' => false,
        'visibility_required' => ['elements' => ['property', 'method']],
        'phpdoc_separation' =>  ['skip_unlisted_annotations' => true],
        'fully_qualified_strict_types' => false,
    ])
    ->setFinder($finder);
