<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->ignoreDotFiles(false)
    ->ignoreVCS(true)
;

return (new Config())
    ->setCacheFile('/tmp/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'declare_strict_types' => true,
        'yoda_style' => true,
        'php_unit_internal_class' => false,
    ])
    ->setUsingCache(true)
;
