<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/vendor/app-verk/feevapp-vendor/src',
        __DIR__ . '/tests',
    ])
    ->notPath('DependencyInjection/Configuration.php')
    ->name('*.php')
;

require_once 'vendor/app-verk/feevapp-vendor/CsFixerConfig.php';

return CsFixerConfig::create()->setFinder($finder);
