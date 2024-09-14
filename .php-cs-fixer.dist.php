<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle',
        'src/Kunstmaan/CookieBundle/Resources/skeleton',
        'node_modules',
    ])
    // Temporary exclude translator class to avoid removing warmUp parameter docblock which is needed to silence a deprecation.
    ->notPath('src/Kunstmaan/TranslatorBundle/Service/Translator/Translator.php')
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
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
