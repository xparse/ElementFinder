<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\Collection\StringCollection;
use Xparse\ElementFinder\DomNodeListAction\DomNodeListActionInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface ElementFinderInterface
{

    /**
     * @throws \Exception
     */
    public function content(string $expression, bool $outerContent = false): StringCollection;


    /**
     * You can remove elements and attributes
     *
     * ```php
     * $html = $html->remove("//span/@class");
     * $html = $html->remove("//input");
     * ```
     *
     */
    public function remove(string $expression): ElementFinderInterface;

    public function modify(string $expression, DomNodeListActionInterface $action): ElementFinderInterface;

    /**
     * Get nodeValue of the node
     *
     * @throws \Exception
     */
    public function value(string $expression): Collection\StringCollection;


    /**
     * Return array of keys and values
     *
     * @throws \Exception
     */
    public function keyValue(string $keyExpression, string $valueExpression): array;


    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function object(string $expression, bool $outerHtml = false): ObjectCollection;


    /**
     * @throws \InvalidArgumentException
     */
    public function element(string $expression): Collection\ElementCollection;

    /**
     * @return string[]
     */
    public function getLoadErrors(): array;
}
