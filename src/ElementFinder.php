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
     */
    const DOCUMENT_HTML = 'html';

    /**
     * Xml document type
     */
    const DOCUMENT_XML = 'xml';

    /**
     * Hide errors
     *
     * @var int
     */
    protected $options = null;

    /**
     * html or xml
     *
     * @var string
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
      $this->dom = new \DomDocument();

      $data = trim($data);

      if (empty($documentType)) {
        $documentType = static::DOCUMENT_HTML;
      }

      if ($documentType != static::DOCUMENT_HTML and $documentType != static::DOCUMENT_XML) {
        throw new \InvalidArgumentException("Doc type not valid. use xml or html");
      }

      $this->type = $documentType;

      if (!empty($options)) {
        $this->options = $options;
      } else {
        $this->options = LIBXML_NOCDATA & LIBXML_NOERROR;
      }

      $this->setData($data);
    }

    public function __destruct() {
      unset($this->dom);
      unset($this->xpath);
    }

    /**
     * @param string $xpath
     * @param bool $outerHtml
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     */
    public function html($xpath, $outerHtml = false) {

      $items = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\StringCollection();

      foreach ($items as $key => $node) {
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
     * @param $xpath
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
     * @param $xpath
     * @param bool $fromOuterHtml
     * @throws \Exception
     * @return \Xparse\ElementFinder\ElementFinder\ObjectCollection
     */
    public function object($xpath, $fromOuterHtml = false) {
      $items = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\ObjectCollection();
      foreach ($items as $node) {
        /** @var \DOMElement $node */
        if ($fromOuterHtml) {
          $html = Helper::getOuterHtml($node);
        } else {
          $html = Helper::getInnerHtml($node);
        }

        $obj = new ElementFinder($html, $this->getType(), $this->getOptions());

        $collection->append($obj);
      }

      return $collection;
    }

    /**
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
      $this->dom->registerNodeClass("DOMElement", "\Xparse\ElementFinder\ElementFinder\Element");
      $nodeList = $this->xpath->query($xpath);

      $collection = new \Xparse\ElementFinder\ElementFinder\ElementCollection();
      foreach ($nodeList as $item) {
        $collection->append($item);
      }

      return $collection;
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
     * Match regex in document
     * ```php
     *  $tels = $html->match('!([0-9]{4,6})!');
     * ```
     *
     * @param string $regex
     * @param integer $i
     * @return array
     */
    public function match($regex, $i = 1) {
      $documentHtml = $this->html('.')->getFirst();
      preg_match_all($regex, $documentHtml, $matchedData);

      $elements = new \Xparse\ElementFinder\ElementFinder\StringCollection();
      if (isset($matchedData[$i])) {
        $elements->setItems($matchedData[$i]);
        return $elements;
      } else {
        return $elements;
      }
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
     * @return string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * @return int
     */
    public function getOptions() {
      return $this->options;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function setData($data) {
      if (empty($data)) {
        $data = '<div data-document-is-empty></div>';
      }
      libxml_use_internal_errors();
      libxml_disable_entity_loader();
      libxml_clear_errors();
      if ($this->type == static::DOCUMENT_HTML) {
        $data = \Xparse\ElementFinder\Helper::safeEncodeStr($data);
        $data = mb_convert_encoding($data, 'HTML-ENTITIES', "UTF-8");
        $this->dom->loadHTML($data, $this->options);
      } else {
        $this->dom->loadXML($data, $this->options);
      }

      unset($this->xpath);
      $this->xpath = new \DomXPath($this->dom);

      return $this;
    }

  }