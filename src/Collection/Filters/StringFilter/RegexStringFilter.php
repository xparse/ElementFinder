<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Filters\StringFilter;

/**
 *
 */
class RegexStringFilter implements StringFilterInterface
{

    /**
     * @var string
     */
    private $regex;


    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }


    final public function valid(string $input): bool
    {
        return preg_match($this->regex, $input) === 1;
    }
}
