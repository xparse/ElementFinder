<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\Collection\StringCollection;
use Xparse\ElementFinder\ElementFinder\ElementFinderModifierInterface;

/**
 * @author Ivan Scherbak <alotofall@gmail.com>
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
     * @return ElementFinderInterface
     */
    public function remove($expression) : ElementFinderInterface;

    /**
     * @param string $expression
     * @param ElementFinderModifierInterface $modifier
     * @return ElementFinderInterface
     */
    public function modify(string $expression, ElementFinderModifierInterface $modifier): ElementFinderInterface;

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
     * @return ObjectCollection
     * @throws \InvalidArgumentException
     */
    public function object($expression, $outerHtml = false): ObjectCollection;


    /**
     * @param string $expression
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    public function element($expression): Collection\ElementCollection;


    /**
     * @return array
     */
    public function getLoadErrors(): array;
}
