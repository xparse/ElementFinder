<?php

declare(strict_types=1);

namespace Xparse\ElementFinder;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DomXPath;
use Exception;
use InvalidArgumentException;
use LibXMLError;
use RuntimeException;
use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\Collection\StringCollection;
use Xparse\ElementFinder\DomNodeListAction\DomNodeListActionInterface;
use Xparse\ElementFinder\DomNodeListAction\RemoveNodes;
use Xparse\ElementFinder\ElementFinder\Element;
use Xparse\ElementFinder\ExpressionTranslator\ExpressionTranslatorInterface;
use Xparse\ElementFinder\ExpressionTranslator\XpathExpression;
use Xparse\ElementFinder\Helper\NodeHelper;
use Xparse\ElementFinder\Helper\StringHelper;

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
    final public const DOCUMENT_HTML = 0;

    /**
     * Xml document type
     *
     * @var int
     */
    final public const DOCUMENT_XML = 1;

    private int $type;

    private DOMDocument $dom;

    private DomXPath $xpath;

    private ExpressionTranslatorInterface $expressionTranslator;

    /**
     * @var LibXMLError[]
     */
    private array $loadErrors = [];

    /**
     * Example:
     * new ElementFinder("<html><div>test </div></html>", ElementFinder::HTML);
     *
     * @throws Exception
     */
    public function __construct(
        string $data,
        int $documentType = null,
        ExpressionTranslatorInterface $translator = null
    ) {
        $this->dom = new DomDocument();
        $this->expressionTranslator = $translator ?? new XpathExpression();
        $this->dom->registerNodeClass(DOMElement::class, Element::class);
        $this->type = $documentType ?? static::DOCUMENT_HTML;
        $this->setData($data ?: '<html></html>');
    }

    public function __destruct()
    {
        unset($this->dom, $this->xpath);
    }

    public function __clone()
    {
        $this->dom = clone $this->dom;
        $this->xpath = new DomXPath($this->dom);
    }

    /**
     * @throws Exception
     */
    final public function content(string $expression, bool $outerContent = false): StringCollection
    {
        $items = $this->query($expression);
        $result = [];
        foreach ($items as $node) {
            if ($outerContent) {
                $result[] = NodeHelper::getOuterContent($node, $this->type);
            } else {
                $result[] = NodeHelper::getInnerContent($node, $this->type);
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
     * @throws Exception
     */
    final public function value(string $expression): StringCollection
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
     * @throws Exception
     */
    final public function keyValue(string $keyExpression, string $valueExpression): array
    {
        $keyNodes = $this->query($keyExpression);
        $valueNodes = $this->query($valueExpression);
        if ($keyNodes->length !== $valueNodes->length) {
            throw new RuntimeException('Keys and values must have equal numbers of elements');
        }
        $result = [];
        foreach ($keyNodes as $index => $node) {
            $result[$node->nodeValue] = $valueNodes->item($index)->nodeValue;
        }
        return $result;
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    final public function object(string $expression, bool $outerHtml = false): ObjectCollection
    {
        $type = $this->type;
        $items = $this->query($expression);
        $result = [];
        foreach ($items as $node) {
            assert($node instanceof DOMElement);
            $html = $outerHtml
                ? NodeHelper::getOuterContent($node, $this->type)
                : NodeHelper::getInnerContent($node, $this->type);
            if (trim($html) === '') {
                $html = '<html data-document-is-empty></html>';
            }
            if ($this->type === static::DOCUMENT_XML and ! str_contains($html, '<?xml')) {
                $html = '<root>' . $html . '</root>';
            }
            $result[] = new ElementFinder($html, $type, $this->expressionTranslator);
        }
        return new ObjectCollection($result);
    }

    /**
     * @throws InvalidArgumentException
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

    final public function getLoadErrors(): array
    {
        return $this->loadErrors;
    }

    /**
     * @return $this
     * @throws Exception
     */
    private function setData(string $data): self
    {
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = false;
        if (\LIBXML_VERSION < 20900) {
            $disableEntities = libxml_disable_entity_loader();
        }

        if (static::DOCUMENT_HTML === $this->type) {
            $data = StringHelper::safeEncodeStr($data);

            //Analogue of mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8')
            //Usage of mb_convert_encoding with encoding to HTML_ENTITIES is deprecated since php version 8.2
            //When passing data to ElementFinder in an encoding other than UTF-8, any unrecognized characters will be ignored
            $data = mb_encode_numericentity(
                htmlspecialchars_decode(
                    htmlentities($data, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8', false),
                    ENT_NOQUOTES
                ),
                [0x80, 0x10FFFF, 0, ~0],
                'UTF-8'
            );

            $this->dom->loadHTML($data, LIBXML_NOCDATA & LIBXML_NOERROR);
        } elseif (static::DOCUMENT_XML === $this->type) {
            $this->dom->loadXML($data, LIBXML_NOCDATA & LIBXML_NOERROR);
        } else {
            throw new InvalidArgumentException('Doc type not valid. use xml or html');
        }
        $this->loadErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        if (\LIBXML_VERSION < 20900) {
            libxml_disable_entity_loader($disableEntities);
        }
        unset($this->xpath);
        $this->xpath = new DomXPath($this->dom);
        return $this;
    }

    /**
     * @see element
     * Fetch nodes from document
     */
    private function query(string $expression): DOMNodeList
    {
        return $this->xpath->query(
            $this->expressionTranslator->convertToXpath($expression)
        );
    }
}
