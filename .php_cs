<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->setUsingCache(true)
    ->fixers([
        'psr0',
        'encoding',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'parenthesis',
        'php_closing_tag',
        'visibility',
        'multiline_array_trailing_comma',
        'namespace_no_leading_whitespace',
        'remove_lines_between_uses',
        'single_array_no_trailing_comma',
        'spaces_before_semicolon',
        'spaces_cast',
        'unused_use',
        'concat_with_spaces',
        'ordered_use',
        'short_array_syntax'
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__)
            ->exclude([
                'app',
                'web',
            ])
            ->notPath('src/Korobi/WebBundle/Resources/views/controller/generic/style_guide.html.twig')
    );
