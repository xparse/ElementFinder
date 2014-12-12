<?php

  namespace Xparse\ElementFinder\Test;

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
      $fileData = file_get_contents($this->getDemoDataDirectoryPath() . '/test.html');
      $html = new \Xparse\ElementFinder\ElementFinder($fileData);
      return $html;
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlDataObject() {
      $fileData = file_get_contents($this->getDemoDataDirectoryPath() . '/data.html');
      $html = new \Xparse\ElementFinder\ElementFinder($fileData);
      return $html;
    }

  }