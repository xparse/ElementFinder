<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Dummy;

use Xparse\ElementFinder\Collection\Filters\StringFilter\StringFilterInterface;

final class WithLetterFilter implements StringFilterInterface
{
    public function __construct(private string $letter)
    {
    }

    public function valid(string $input): bool
    {
        return str_contains($input, $this->letter);
    }
}
