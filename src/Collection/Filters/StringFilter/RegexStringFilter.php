<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Filters\StringFilter;

class RegexStringFilter implements StringFilterInterface
{
    public function __construct(
        private string $regex
    ) {
    }

    final public function valid(string $input): bool
    {
        return preg_match($this->regex, $input) === 1;
    }
}
