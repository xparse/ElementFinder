<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __FILE__,
    ]);

    $ecsConfig->rules([
        NoUnusedImportsFixer::class,
        VoidReturnFixer::class,
        DeclareStrictTypesFixer::class,
    ]);

    // this way you can add sets - group of rules
    $ecsConfig->sets([SetList::SPACES, SetList::ARRAY, SetList::DOCBLOCK, SetList::NAMESPACES, SetList::COMMENTS, SetList::PSR_12]);
};
