<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\Collection\StringCollection;
use Xparse\ElementFinder\ElementFinder\ElementFinderModifierInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
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
    public function remove(string $expression): ElementFinderInterface;

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
    public function value(string $expression): Collection\StringCollection;


    /**
     * Return array of keys and values
     *
     * @param string $keyExpression
     * @param string $valueExpression
     * @return array
     * @throws \Exception
     */
    public function keyValue(string $keyExpression, string $valueExpression): array;


    /**
     * @param string $expression
     * @param bool $outerHtml
     * @return ObjectCollection
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function object(string $expression, bool $outerHtml = false): ObjectCollection;


    /**
     * @param string $expression
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    public function element(string $expression): Collection\ElementCollection;


    /**
     * @return array
     */
    public function getLoadErrors(): array;
}
