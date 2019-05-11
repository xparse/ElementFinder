<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Modify\StringModify;

interface StringModifyInterface
{
    public function modify(string $input): string;
}
