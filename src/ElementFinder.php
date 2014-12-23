<?php

  namespace Xparse\ElementFinder;

  /**
   * @author  Ivan Scherbak <dev@funivan.com> 03.08.2011 10:25:00
   * @link    <funivan.com>
   *
   */
  class ElementFinder {

    /**
     * Html document type
     *
     * @var boolean
     */
    const DOCUMENT_HTML = 0;

    /**
     * Xml document type
     * @var boolean
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
     * @var boolean
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
     * Holder for regex
     *
     * @var array
     */
    protected $matchRegex = array();

    /**
     *
     *
     * Example:
     * new ElementFinder("<html><div>test </div></html>", ElementFinder::HTML);
     *
     * @param null|string $data
     * @param null|string $documentType
     * @param int $options
     */
    public function __construct($data = null, $documentType = null, $options = null) {

      if (!is_string($data) or empty($data)) {
        throw new \InvalidArgumentException('Expect not empty string');
      }

      $this->dom = new \DomDocument();

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
      unset($this->dom);
      unset($this->xpath);
    }

    /**
     * @param $data
     * @return $this
     */
    protected function setData($data) {

      $internalErrors = libxml_use_internal_errors(true);
      $disableEntities = libxml_disable_entity_loader(true);

      if ($this->type == static::DOCUMENT_HTML) {
        $data = \Xparse\ElementFinder\Helper::safeEncodeStr($data);
        $data = mb_convert_encoding($data, 'HTML-ENTITIES', "UTF-8");
        $this->dom->loadHTML($data, $this->options);
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
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     */
    public function html($xpath, $outerHtml = false) {

      $items = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\StringCollection();

      foreach ($items as $node) {
        if ($outerHtml) {
          $html = Helper::getOuterHtml($node);
        } else {
          $html = Helper::getInnerHtml($node);
        }

        $collection->append($html);

      }

      return $collection;
    }

    /**
     * Remove node by xpath
     *
     * ```
     * $page->remove('//a')
     * ```
     *
     * @param string $xpath
     * @return $this
     */
    public function remove($xpath) {

      $items = $this->xpath->query($xpath);

      foreach ($items as $key => $node) {
        $node->parentNode->removeChild($node);
      }

      return $this;
    }


    /**
     * Get nodeValue of node
     *
     * @param string $xpath
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     */
    public function value($xpath) {
      $items = $this->xpath->query($xpath);
      $collection = new \Xparse\ElementFinder\ElementFinder\StringCollection();
      foreach ($items as $node) {
        $collection->append($node->nodeValue);
      }
      return $collection;
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
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     */
    public function attribute($xpath) {
      $items = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\StringCollection();
      foreach ($items as $item) {
        /** @var \DOMAttr $item */
        $collection->append($item->value);
      }

      return $collection;
    }

    /**
     * @param string $xpath
     * @param bool $outerHtml
     * @throws \Exception
     * @return \Xparse\ElementFinder\ElementFinder\ObjectCollection
     */
    public function object($xpath, $outerHtml = false) {
      $items = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\ObjectCollection();
      foreach ($items as $node) {
        /** @var \DOMElement $node */
        if ($outerHtml) {
          $html = Helper::getOuterHtml($node);
        } else {
          $html = Helper::getInnerHtml($node);
        }

        if (trim($html) === "") {
          $html = $this->getEmptyDocumentHtml();
        }

        $obj = new ElementFinder($html, $this->getType(), $this->getOptions());

        $collection->append($obj);
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
      return $this->xpath->query($xpath);
    }


    /**
     * @param string $xpath
     * @return \Xparse\ElementFinder\ElementFinder\ElementCollection
     */
    public function elements($xpath) {
      $this->dom->registerNodeClass('DOMElement', '\Xparse\ElementFinder\ElementFinder\Element');
      $nodeList = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\ElementCollection();
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
     * @return array
     * @throws \Exception
     */
    public function match($regex, $i = 1) {

      if (!is_callable($i) and !is_numeric($i)) {
        throw new \Exception('Expect integer or callback');
      }

      $documentHtml = $this->html('.')->getFirst();

      preg_match_all($regex, $documentHtml, $matchedData);

      $elements = new \Xparse\ElementFinder\ElementFinder\StringCollection();

      if (is_int($i)) {

        if (isset($matchedData[$i])) {
          $elements->setItems($matchedData[$i]);
        }

        return $elements;
      }

      $items = $i($matchedData);

      if (!is_array($items)) {
        throw new \Exception("Invalid value. Expect array from callback");
      }

      foreach ($items as $string) {
        if (is_string($string) or is_float($string) or is_integer($string)) {
          continue;
        }

        throw new \Exception("Invalid value. Expect array of strings:" . gettype($string));
      }

      $elements->setItems($items);

      return $elements;
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

      if (trim($newDoc) === "") {
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
     */
    public function getNodeItems($path, array $itemsParams) {
      $result = array();
      $nodes = $this->object($path);
      foreach ($nodes as $nodeIndex => $nodeDocument) {
        $nodeValues = array();

        foreach ($itemsParams as $elementResultIndex => $elementResultPath) {
          /** @var ElementFinder $nodeDocument */
          $nodeValues[$elementResultIndex] = $nodeDocument->html($elementResultPath)->item(0);
        }
        $result[$nodeIndex] = $nodeValues;
      }

      return $result;
    }

    /**
     * Return type of document
     *
     * @return boolean
     */
    public function getType() {
      return $this->type;
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
     * @return string
     */
    protected function getEmptyDocumentHtml() {
      return '<html data-document-is-empty></html>';
    }

    /**
     * @param boolean $documentType
     * @return $this
     */
    protected function setDocumentType($documentType) {

      if ($documentType !== static::DOCUMENT_HTML and $documentType !== static::DOCUMENT_XML) {
        throw new \InvalidArgumentException("Doc type not valid. use xml or html");
      }

      $this->type = $documentType;

      return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    protected function setDocumentOption($options) {

      if (!is_integer($options)) {
        throw new \InvalidArgumentException("Expect int options");
      }

      $this->options = $options;

      return $this;
    }

  }