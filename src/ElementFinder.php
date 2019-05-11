<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\Collection\StringCollection;
use Xparse\ElementFinder\DomNodeListAction\DomNodeListActionInterface;
use Xparse\ElementFinder\DomNodeListAction\RemoveNodes;
use Xparse\ElementFinder\ElementFinder\Element;
use Xparse\ElementFinder\Helper\NodeHelper;
use Xparse\ElementFinder\Helper\StringHelper;
use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;
use Xparse\ExpressionTranslator\XpathExpression;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ElementFinder implements ElementFinderInterface
{

    /**
     * Html document type
     *
     * @var int
     */
    public const DOCUMENT_HTML = 0;

    /**
     * Xml document type
     *
     * @var int
     */
    public const DOCUMENT_XML = 1;

    /**
     * Current document type
     *
     * @var int
     */
    private $type;

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DomXPath
     */
    private $xpath;

    /**
     * @var ExpressionTranslatorInterface
     */
    private $expressionTranslator;

    /**
     * @var array
     */
    private $loadErrors = [];


    /**
     *
     *
     * Example:
     * new ElementFinder("<html><div>test </div></html>", ElementFinder::HTML);
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct(string $data, int $documentType = null, ExpressionTranslatorInterface $translator = null)
    {
        if ('' === $data) {
            throw new \InvalidArgumentException('Expect not empty string');
        }
        $this->dom = new \DomDocument();
        $this->expressionTranslator = $translator ?? new XpathExpression();
        $this->dom->registerNodeClass(\DOMElement::class, Element::class);
        $this->type = $documentType ?? static::DOCUMENT_HTML;
        $this->setData($data);
    }


    public function __destruct()
    {
        unset($this->dom, $this->xpath);
    }


    public function __clone()
    {
        $this->dom = clone $this->dom;
        $this->xpath = new \DomXPath($this->dom);
    }

    /**
     * @throws \Exception
     */
    final public function content(string $expression, bool $outerContent = false): StringCollection
    {
        $items = $this->query($expression);
        $result = [];
        foreach ($items as $node) {
            if ($outerContent) {
                $result[] = NodeHelper::getOuterContent($node);
            } else {
                $result[] = NodeHelper::getInnerContent($node);
            }
        }
        return new StringCollection($result);
    }


    /**
     * You can remove elements and attributes
     *
     * ```php
     * $html = $html->remove("//span/@class");
     * $html = $html->remove("//input");
     * ```
     */
    final public function remove(string $expression): ElementFinderInterface
    {
        return $this->modify($expression, new RemoveNodes());
    }


    final public function modify(string $expression, DomNodeListActionInterface $action): ElementFinderInterface
    {
        $result = clone $this;
        $action->execute(
            $result->query($expression)
        );
        return $result;
    }

    /**
     * Get nodeValue of node
     *
     * @throws \Exception
     */
    final public function value(string $expression): Collection\StringCollection
    {
        $items = $this->query($expression);
        $result = [];
        foreach ($items as $node) {
            $result[] = $node->nodeValue;
        }
        return new StringCollection($result);
    }


    /**
     * Return array of keys and values
     *
     * @throws \Exception
     */
    final public function keyValue(string $keyExpression, string $valueExpression): array
    {
        $keyNodes = $this->query($keyExpression);
        $valueNodes = $this->query($valueExpression);
        if ($keyNodes->length !== $valueNodes->length) {
            throw new \RuntimeException('Keys and values must have equal numbers of elements');
        }
        $result = [];
        foreach ($keyNodes as $index => $node) {
            $result[$node->nodeValue] = $valueNodes->item($index)->nodeValue;
        }
        return $result;
    }


    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    final public function object(string $expression, bool $outerHtml = false): ObjectCollection
    {
        $type = $this->type;
        $items = $this->query($expression);
        $result = [];
        foreach ($items as $node) {
            assert($node instanceof \DOMElement);
            $html = $outerHtml
                ? NodeHelper::getOuterContent($node)
                : NodeHelper::getInnerContent($node);
            if (trim($html) === '') {
                $html = '<html data-document-is-empty></html>';
            }
            if ($this->type === static::DOCUMENT_XML and strpos($html, '<?xml') === false) {
                $html = '<root>' . $html . '</root>';
            }
            $result[] = new ElementFinder($html, $type, $this->expressionTranslator);
        }
        return new ObjectCollection($result);
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function element(string $expression): ElementCollection
    {
        $nodeList = $this->query($expression);
        $items = [];
        foreach ($nodeList as $item) {
            $items[] = clone $item;
        }
        return new ElementCollection($items);
    }


    /**
     */
    final public function getLoadErrors(): array
    {
        return $this->loadErrors;
    }


    /**
     * @return $this
     * @throws \Exception
     */
    private function setData(string $data): self
    {
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader();

        if (static::DOCUMENT_HTML === $this->type) {
            $data = StringHelper::safeEncodeStr($data);
            $data = mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8');
            $this->dom->loadHTML($data, LIBXML_NOCDATA & LIBXML_NOERROR);
        } elseif (static::DOCUMENT_XML === $this->type) {
            $this->dom->loadXML($data, LIBXML_NOCDATA & LIBXML_NOERROR);
        } else {
            throw new \InvalidArgumentException('Doc type not valid. use xml or html');
        }
        $this->loadErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);
        unset($this->xpath);
        $this->xpath = new \DomXPath($this->dom);
        return $this;
    }

    /**
     * @see element
     * Fetch nodes from document
     */
    private function query(string $expression): \DOMNodeList
    {
        return $this->xpath->query(
            $this->expressionTranslator->convertToXpath($expression)
        );
    }
}
