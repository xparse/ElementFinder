<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Dummy;

use Xparse\ElementFinder\Collection\Modify\StringModify\StringModifyInterface;

class JoinedBy implements StringModifyInterface
{
    /**
     * @var string
     */
    private $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    public function modify(string $input): string
    {
        return $input . $this->str . $input;
    }
}
