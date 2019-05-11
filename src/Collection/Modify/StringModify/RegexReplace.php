<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection\Modify\StringModify;

/**
 *
 */
class RegexReplace implements StringModifyInterface
{

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;


    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }


    final public function modify(string $input): string
    {
        return preg_replace($this->from, $this->to, $input);
    }
}
