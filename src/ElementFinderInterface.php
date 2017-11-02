<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\Collection\StringCollection;

/**
 * @author Ivan Scherbak <dev@funivan.com>
 */
interface ElementFinderInterface
{

    /**
     * @param string $expression
     * @param bool $outerContent
     * @return StringCollection
     * @throws \Exception
     */
    public function content(string $expression, bool $outerContent = false): StringCollection;


    /**
     * You can remove elements and attributes
     *
     * ```php
     * $html->remove("//span/@class");
     *
     * $html->remove("//input");
     * ```
     *
     * @param string $expression
     * @return $this
     */
    public function remove($expression);


    /**
     * Get nodeValue of node
     *
     * @param string $expression
     * @return StringCollection
     * @throws \Exception
     */
    public function value($expression): Collection\StringCollection;


    /**
     * Return array of keys and values
     *
     * @param string $keyExpression
     * @param string $valueExpression
     * @throws \Exception
     * @return array
     */
    public function keyValue(string $keyExpression, string $valueExpression): array;


    /**
     * @param string $expression
     * @param bool $outerHtml
     * @throws \Exception
     * @return \Xparse\ElementFinder\Collection\ObjectCollection
     * @throws \InvalidArgumentException
     */
    public function object($expression, $outerHtml = false): Collection\ObjectCollection;


    /**
     * @param string $expression
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    public function element($expression): Collection\ElementCollection;


    /**
     * Match regex in document
     * ```php
     *  $tels = $html->match('!([0-9]{4,6})!');
     * ```
     *
     * @param string $regex
     * @param int|callable $i
     * @return StringCollection
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function match($regex, $i = 1): Collection\StringCollection;


    /**
     * @return array
     */
    public function getLoadErrors(): array;
}
