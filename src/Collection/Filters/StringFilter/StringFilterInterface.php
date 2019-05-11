<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Filters\StringFilter;

/**
 *
 */
interface StringFilterInterface
{
    public function valid(string $input): bool;
}
