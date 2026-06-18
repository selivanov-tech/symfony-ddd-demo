<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/apps', __DIR__ . '/tests'])
    ->name('*.php');

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(false)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'no_extra_blank_lines' => true,
    ])
    ->setFinder($finder);
