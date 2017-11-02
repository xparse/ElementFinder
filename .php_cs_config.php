<?php
$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'psr4' => true,
        'phpdoc_indent' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_statement' => false,
        'strict_comparison' => true,
        'strict_param' => true,
        'no_null_property_initialization' => true,
        'yoda_style' => false,
        'ordered_imports' => ['sortAlgorithm' => 'alpha'],
        'ordered_class_elements' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public',
            'method_protected',
            'method_private'
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
          ->in(__DIR__.'/src')
          ->in(__DIR__.'/tests')
    )
;
// special handling of fabbot.io service if it's using too old PHP CS Fixer version
try {
    PhpCsFixer\FixerFactory::create()
        ->registerBuiltInFixers()
        ->registerCustomFixers($config->getCustomFixers())
        ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()));
} catch (PhpCsFixer\ConfigurationException\InvalidConfigurationException $e) {
    $config->setRules([]);
} catch (UnexpectedValueException $e) {
    $config->setRules([]);
}
return $config;