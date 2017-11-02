<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Dummy;

use Xparse\ElementFinder\Collection\Filters\StringFilter\StringFilterInterface;

class WithLetterFilter implements StringFilterInterface
{
    /**
     * @var string
     */
    private $letter;

    public function __construct(string $letter)
    {
        $this->letter = $letter;
    }

    public function valid(string $input): bool
    {
        return strpos($input, $this->letter) !== false;
    }
}
