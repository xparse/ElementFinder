<?php

  namespace Test\Xparse\ElementFinder;

  use Xparse\ElementFinder\ElementFinder;

  /**
   * @codeCoverageIgnore
   */
  abstract class Main extends \PHPUnit_Framework_TestCase {

    /**
     * @return string
     */
    protected function getDemoDataDirectoryPath() {
      return __DIR__ . '/demo-data/';
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlTestObject() {
      return $this->initFromFile('test.html');
    }
    
    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getXmlTestObject() {
      return $this->initFromFile('xml-test-data.xml', ElementFinder::DOCUMENT_XML);
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getXmlErrorTestObject() {
      return $this->initFromFile('xml-error-test-data.xml', ElementFinder::DOCUMENT_XML);
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlDataObject() {
      return $this->initFromFile('data.html');
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getNodeItemsHtmlObject() {
      return $this->initFromFile('node-items.html');
    }


    /**
     * @param string $file
     * @param null $type
     * @return \Xparse\ElementFinder\ElementFinder
     */
    protected function initFromFile($file, $type = null) {
      $fileData = file_get_contents($this->getDemoDataDirectoryPath() . DIRECTORY_SEPARATOR . $file);
      $html = new \Xparse\ElementFinder\ElementFinder($fileData, $type);
      return $html;
    }

  }