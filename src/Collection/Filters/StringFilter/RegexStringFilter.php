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


    /**
     * @param string $regex
     */
    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }


    /**
     * @param string $input
     * @return bool
     */
    final public function valid(string $input): bool
    {
        return preg_match($this->regex, $input) === 1;
    }
}
