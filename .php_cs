<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'tests', 'demos']);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'method_argument_space' => false,
    ])
    ->setFinder($finder);
