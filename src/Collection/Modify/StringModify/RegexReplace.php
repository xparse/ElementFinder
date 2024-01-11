<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Modify\StringModify;

class RegexReplace implements StringModifyInterface
{
    public function __construct(
        private readonly string $from,
        private readonly string $to
    ) {
    }

    final public function modify(string $input): string
    {
        return preg_replace($this->from, $this->to, $input);
    }
}
