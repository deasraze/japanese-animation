<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('docker')
            ->exclude('config')
            ->exclude('var')
            ->exclude('vendor')
            ->exclude('public/bundles')
            ->exclude('public/build')
            ->notPath('bin/console')
            ->notPath('public/index.php')
    )
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,

        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],

        'phpdoc_to_comment' => false,
        'phpdoc_separation' => false,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'phpdoc_align' => false,

        'operator_linebreak' => false,

        'global_namespace_import' => true,

        'blank_line_before_statement' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

        'fopen_flags' => ['b_mode' => true],

        'php_unit_strict' => false,
        'php_unit_test_class_requires_covers' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],

        'yoda_style' => false,

        'final_public_method_for_abstract_class' => true,
        'self_static_accessor' => true,

        'static_lambda' => true,

        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,

        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setCacheFile(__DIR__.'/var/.php_cs.cache');
