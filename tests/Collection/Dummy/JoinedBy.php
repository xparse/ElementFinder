<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Dummy;

use Xparse\ElementFinder\Collection\Modify\StringModify\StringModifyInterface;

final class JoinedBy implements StringModifyInterface
{
    public function __construct(
        private string $str
    ) {
    }

    public function modify(string $input): string
    {
        return $input . $this->str . $input;
    }
}
