<?php
declare(strict_types=1);

class CsFixerConfig extends \PhpCsFixer\Config
{
    public function __construct($name = 'default')
    {
        parent::__construct($name);

        $this
            ->setRiskyAllowed(true)
            ->setRules([
                '@PhpCsFixer' => true,
                '@DoctrineAnnotation' => true,
                'align_multiline_comment' => ['comment_type' => 'all_multiline'],
                'array_syntax' => ['syntax' => 'short'],
                'binary_operator_spaces' => true,
                'blank_line_after_opening_tag' => true,
                'blank_line_before_return' => true,
                'cast_spaces' => true,
                'class_attributes_separation' => true,
                'combine_nested_dirname' => true,
                'comment_to_phpdoc' => true,
                'compact_nullable_typehint' => true,
                'concat_space' => ['spacing' => 'one'],
                'declare_equal_normalize' => ['space' => 'single'],
                'declare_strict_types' => true,
                'dir_constant' => true,
                'ereg_to_preg' => true,
                'fopen_flag_order' => true,
                'fopen_flags' => true,
                'function_to_constant' => true,
                'function_typehint_space' => true,
                'is_null' => true,
                'linebreak_after_opening_tag' => true,
                'list_syntax' => true,
                'logical_operators' => true,
                'lowercase_constants' => true,
                'mb_str_functions' => true,
                'method_separation' => true,
                'native_constant_invocation' => true,
                'no_alias_functions' => true,
                'no_blank_lines_after_class_opening' => true,
                'no_blank_lines_after_phpdoc' => true,
                'no_extra_consecutive_blank_lines' => true,
                'no_mixed_echo_print' => true,
                'no_php4_constructor' => true,
                'no_unneeded_final_method' => true,
                'no_unset_on_property' => true,
                'no_unused_imports' => true,
                'no_useless_else' => true,
                'no_useless_return' => true,
                'no_whitespace_before_comma_in_array' => true,
                'no_whitespace_in_blank_line' => true,
                'nullable_type_declaration_for_default_null_value' => [
                    'use_nullable_type_declaration' => false
                ],
                'object_operator_without_whitespace' => true,
                'ordered_class_elements' => true,
                'ordered_imports' => true,
                'ordered_interfaces' => true,
                'php_unit_construct' => true,
                'php_unit_dedicate_assert' => true,
                'php_unit_dedicate_assert_internal_type' => true,
                'php_unit_set_up_tear_down_visibility' => true,
                'php_unit_test_annotation' => true,
                'php_unit_test_case_static_method_calls' => [
                    'call_type' => 'this'
                ],
                'php_unit_test_class_requires_covers' => false,
                'phpdoc_add_missing_param_annotation' => true,
                'phpdoc_align' => true,
                'phpdoc_indent' => true,
                'phpdoc_line_span' => true,
                'pre_increment' => true,
                'protected_to_private' => true,
                'random_api_migration' => true,
                'return_type_declaration' => ['space_before'=>'none'],
                'self_accessor' => true,
                'self_static_accessor' => true,
                'set_type_to_cast' => true,
                'simplified_null_return' => true,
                'single_blank_line_before_namespace' => true,
                'single_quote' => true,
                'ternary_operator_spaces' => true,
                'ternary_to_null_coalescing' => true,
                'void_return' => true,
                'whitespace_after_comma_in_array' => true,
                'yoda_style' => true,
            ])
        ;
    }
}