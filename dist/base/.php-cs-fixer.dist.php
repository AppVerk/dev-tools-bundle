<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->notPath('DependencyInjection/Configuration.php')
    ->name('*.php')
;

require_once 'vendor/app-verk/dev-tools-bundle/CsFixerConfig.php';

return (new CsFixerConfig())->setFinder($finder);
