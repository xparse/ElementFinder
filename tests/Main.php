<?php

  namespace Test\Xparse\ElementFinder;

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
     * @return \Xparse\ElementFinder\ElementFinder
     */
    protected function initFromFile($file) {
      $fileData = file_get_contents($this->getDemoDataDirectoryPath() . DIRECTORY_SEPARATOR . $file);
      $html = new \Xparse\ElementFinder\ElementFinder($fileData);
      return $html;
    }

  }