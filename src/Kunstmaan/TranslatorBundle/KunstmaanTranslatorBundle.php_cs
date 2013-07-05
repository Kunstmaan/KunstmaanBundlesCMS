<?php
use Symfony\CS\FixerInterface;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('buid')
    ->exclude('vendor')
    ->exclude('Resources')
    ->exclude('Tests/app/cache/')
    ->exclude('Tests/app/logs/')
    ->ignoreUnreadableDirs()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers(FixerInterface::ALL_LEVEL)
    ->finder($finder)
;