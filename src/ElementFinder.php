<?php

  namespace Xparse\ElementFinder;

  use Xparse\ElementFinder\ElementFinder\Element;
  use Xparse\ElementFinder\ElementFinder\ElementCollection;
  use Xparse\ElementFinder\ElementFinder\ObjectCollection;
  use Xparse\ElementFinder\ElementFinder\StringCollection;
  use Xparse\ElementFinder\Helper\NodeHelper;
  use Xparse\ElementFinder\Helper\RegexHelper;
  use Xparse\ElementFinder\Helper\StringHelper;
  use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;

  /**
   * @author Ivan Scherbak <dev@funivan.com>
   */
  class ElementFinder {

    /**
     * Html document type
     *
     * @var integer
     */
    const DOCUMENT_HTML = 0;

    /**
     * Xml document type
     *
     * @var integer
     */
    const DOCUMENT_XML = 1;

    /**
     * Hide errors
     *
     * @var int
     */
    protected $options = null;

    /**
     * Current document type
     *
     * @var integer
     */
    protected $type = null;

    /**
     * @var \DOMDocument
     */
    protected $dom = null;

    /**
     * @var \DomXPath
     */
    protected $xpath = null;

    /**
     * @var ExpressionTranslatorInterface
     */
    protected $expressionTranslator = null;


    /**
     *
     *
     * Example:
     * new ElementFinder("<html><div>test </div></html>", ElementFinder::HTML);
     *
     * @param string $data
     * @param null|integer $documentType
     * @param int $options
     * @throws \InvalidArgumentException
     */
    public function __construct($data, $documentType = null, $options = null) {

      if (!is_string($data) or empty($data)) {
        throw new \InvalidArgumentException('Expect not empty string');
      }

      $this->dom = new \DomDocument();

      $this->dom->registerNodeClass('DOMElement', Element::class);

      $documentType = ($documentType !== null) ? $documentType : static::DOCUMENT_HTML;
      $this->setDocumentType($documentType);

      # default options
      $options = ($options !== null) ? $options : (LIBXML_NOCDATA & LIBXML_NOERROR);
      $this->setDocumentOption($options);

      $this->setData($data);

    }


    /**
     *
     * @return string
     */
    public function __toString() {
      $result = $this->html('.')->item(0);
      return (string) $result;
    }


    /**
     *
     */
    public function __destruct() {
      unset($this->dom, $this->xpath);
    }


    /**
     * @param $data
     * @return $this
     */
    protected function setData($data) {

      $internalErrors = libxml_use_internal_errors(true);
      $disableEntities = libxml_disable_entity_loader(true);

      if (static::DOCUMENT_HTML == $this->type) {
        $data = StringHelper::safeEncodeStr($data);
        $data = mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8');
        $this->dom->loadHTML($data);
      } else {
        $this->dom->loadXML($data, $this->options);
      }

      libxml_use_internal_errors($internalErrors);
      libxml_disable_entity_loader($disableEntities);

      unset($this->xpath);
      $this->xpath = new \DomXPath($this->dom);

      return $this;
    }


    /**
     * @param string $xpath
     * @param bool $outerHtml
     * @return StringCollection
     */
    public function html($xpath, $outerHtml = false) {

      $items = $this->query($xpath);

      $collection = new StringCollection();

      foreach ($items as $node) {
        if ($outerHtml) {
          $html = NodeHelper::getOuterHtml($node);
        } else {
          $html = NodeHelper::getInnerHtml($node);
        }

        $collection->append($html);

      }

      return $collection;
    }


    /**
     * You can remove elements and attributes
     *
     * ```php
     * $html->remove("//span/@class");
     *
     * $html->remove("//input");
     * ```
     *
     * @param string $xpath
     * @return $this
     */
    public function remove($xpath) {

      $items = $this->query($xpath);

      if ($items === false) {
        return $this;
      }

      foreach ($items as $key => $node) {
        if ($node instanceof \DOMAttr) {
          $node->ownerElement->removeAttribute($node->name);
        } else {
          $node->parentNode->removeChild($node);
        }

      }

      return $this;
    }


    /**
     * Get nodeValue of node
     *
     * @param string $xpath
     * @return StringCollection
     */
    public function value($xpath) {
      $items = $this->query($xpath);
      $collection = new StringCollection();
      foreach ($items as $node) {
        $collection->append($node->nodeValue);
      }
      return $collection;
    }


    /**
     * Return array of keys and values
     *
     * @param string $baseXpath
     * @param string $keyXpath
     * @param string $valueXpath
     * @throws \Exception
     * @return array
     */
    public function keyValue($baseXpath, $keyXpath, $valueXpath) {
      $keyNodes = $this->query($baseXpath . $keyXpath);
      $valueNodes = $this->query($baseXpath . $valueXpath);

      if ($keyNodes->length != $valueNodes->length) {
        throw new \Exception('Keys and values must have equal numbers of elements');
      }

      $result = [];
      foreach ($keyNodes as $index => $node) {
        $result[$node->nodeValue] = $valueNodes->item($index)->nodeValue;
      }

      return $result;
    }


    /**
     * ```
     * // return all href elements
     *
     * $page->attribute('//a/@href');
     *
     * // get title of first link
     * $page->attribute('//a[1]/@title')-item(0);
     *
     * ```
     * @param $xpath
     * @return StringCollection
     */
    public function attribute($xpath) {
      $items = $this->query($xpath);

      $collection = new StringCollection();
      foreach ($items as $item) {
        /** @var \DOMAttr $item */
        $collection->append($item->nodeValue);
      }

      return $collection;
    }


    /**
     * @param string $xpath
     * @param bool $outerHtml
     * @throws \Exception
     * @return ObjectCollection
     * @throws \InvalidArgumentException
     */
    public function object($xpath, $outerHtml = false) {
      $options = $this->getOptions();
      $type = $this->getType();

      $items = $this->query($xpath);

      $collection = new ObjectCollection();

      foreach ($items as $node) {
        /** @var \DOMElement $node */
        if ($outerHtml) {
          $html = NodeHelper::getOuterHtml($node);
        } else {
          $html = NodeHelper::getInnerHtml($node);
        }

        if (trim($html) === '') {
          $html = $this->getEmptyDocumentHtml();
        }

        $elementFinder = new ElementFinder($html, $type, $options);
        $collection[] = $elementFinder;
      }

      return $collection;
    }


    /**
     * Fetch nodes from document
     *
     * @param string $xpath
     * @return \DOMNodeList
     */
    public function node($xpath) {
      return $this->query($xpath);
    }


    /**
     * @param string $xpath
     * @return ElementCollection
     */
    public function elements($xpath) {
      $nodeList = $this->query($xpath);

      $collection = new ElementCollection();
      foreach ($nodeList as $item) {
        $collection->append($item);
      }

      return $collection;
    }


    /**
     * Match regex in document
     * ```php
     *  $tels = $html->match('!([0-9]{4,6})!');
     * ```
     *
     * @param string $regex
     * @param integer|callable $i
     * @return StringCollection
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function match($regex, $i = 1) {

      $documentHtml = $this->html('.')->getFirst();

      if (is_int($i)) {
        $collection = RegexHelper::match($regex, $i, [$documentHtml]);
      } elseif (is_callable($i)) {
        $collection = RegexHelper::matchCallback($regex, $i, [$documentHtml]);
      } else {
        throw new \InvalidArgumentException('Invalid argument. Expect integer or callable');
      }

      return $collection;
    }


    /**
     * Replace in document and refresh it
     *
     * ```php
     *  $html->replace('!00!', '11');
     * ```
     *
     * @param string $regex
     * @param string $to
     * @return $this
     */
    public function replace($regex, $to = '') {
      $newDoc = $this->html('.', true)->getFirst();
      $newDoc = preg_replace($regex, $to, $newDoc);

      if (trim($newDoc) === '') {
        $newDoc = $this->getEmptyDocumentHtml();
      }

      $this->setData($newDoc);
      return $this;
    }


    /**
     *
     * ```php
     *  $elements = array(
     *    'link'      => '//a@href',
     *    'title'     => '//a',
     *    'shortText' => '//p[2]',
     *    'img'       => '//img/@src',
     *  );
     * $news = $html->getNodeItems('//*[@class="news"]', $params);
     * ```
     * By default we get first element
     * By default we get html property of element
     * Properties to fetch can be set in path //a@rel  for rel property of tag A
     *
     * @param string $path
     * @param array $itemsParams
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getNodeItems($path, array $itemsParams) {
      $result = [];
      $nodes = $this->object($path);
      foreach ($nodes as $nodeIndex => $nodeDocument) {
        $nodeValues = [];

        foreach ($itemsParams as $elementResultIndex => $elementResultPath) {
          /** @var ElementFinder $nodeDocument */
          $nodeValues[$elementResultIndex] = $nodeDocument->html($elementResultPath)->item(0);
        }
        $result[$nodeIndex] = $nodeValues;
      }

      return $result;
    }


    /**
     * @return string
     */
    protected function getEmptyDocumentHtml() {
      return '<html data-document-is-empty></html>';
    }


    /**
     * Return type of document
     *
     * @return int
     */
    public function getType() {
      return $this->type;
    }


    /**
     * @param integer $documentType
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setDocumentType($documentType) {

      if ($documentType !== static::DOCUMENT_HTML and $documentType !== static::DOCUMENT_XML) {
        throw new \InvalidArgumentException('Doc type not valid. use xml or html');
      }

      $this->type = $documentType;

      return $this;
    }


    /**
     * @param $options
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setDocumentOption($options) {

      if (!is_int($options)) {
        throw new \InvalidArgumentException('Expect int options');
      }

      $this->options = $options;

      return $this;
    }


    /**
     * Get current options
     *
     * @return int
     */
    public function getOptions() {
      return $this->options;
    }


    /**
     * @param string $expression
     * @return \DOMNodeList
     */
    private function query($expression) {

      if (!is_null($this->expressionTranslator)) {
        $expression = $this->convertExpression($expression);
      }

      return $this->xpath->query($expression);
    }


    /**
     * @param string $expression
     * @return string
     */
    private function convertExpression($expression) {
      return $this->expressionTranslator->convertToXpath($expression);
    }


    /**
     * @return ExpressionTranslatorInterface
     */
    public function getExpressionTranslator() {
      return $this->expressionTranslator;
    }


    /**
     * @param ExpressionTranslatorInterface $expressionTranslator
     * @return $this
     */
    public function setExpressionTranslator(ExpressionTranslatorInterface $expressionTranslator) {
      $this->expressionTranslator = $expressionTranslator;
      return $this;
    }


  }