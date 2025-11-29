<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->ignoreDotFiles(false)
    ->ignoreVCS(true)
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude(['var', 'vendor']);

return (new Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => false,
        'phpdoc_summary' => false,
        'global_namespace_import' => false,
        'concat_space' => ['spacing' => 'one'],
        'ternary_to_null_coalescing' => true,
        'visibility_required' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'not_operator_with_successor_space' => false,
        'declare_strict_types' => false,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'single_line_empty_body' => false,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ]
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline'
        ],
        'trim_array_spaces' => true,
        'method_chaining_indentation' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
        'phpdoc_align' => [
            'align' => 'left'
        ],
        'class_attributes_separation' => true,
        'combine_consecutive_unsets' => true,
        'linebreak_after_opening_tag' => true,
        'lowercase_static_reference' => true,
        'no_useless_else' => true,
        'no_unused_imports' => true,
        'not_operator_with_space' => false,
        'ordered_class_elements' => true,
        'php_unit_strict' => false,
        'phpdoc_separation' => false,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'multiline_comment_opening_closing' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true,
        ],
        'phpdoc_to_param_type' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
