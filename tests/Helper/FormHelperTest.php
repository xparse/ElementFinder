<?php

  declare(strict_types=1);

  namespace Tests\Xparse\ElementFinder\Helper;

  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper\FormHelper;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class FormHelperTest extends \PHPUnit_Framework_TestCase {

    public function testFormData() {
      $html = '
        <div>
        <form >
          <input type="text" name="test" value="123"/>
          <select name="sf">
            <option value="1">1</option>
            <option value="2" selected="selected">2</option>
          </select>
          <input type="checkbox" name="captcha" checked="checked" value="1"/>
          <textarea name="text">custom text</textarea>
          <select name="sc">
            <option value="16">16</option>
            <option value="15">15</option>
          </select>
        </form>
        </div>
      
      ';
      $page = new ElementFinder($html);
      $formData = FormHelper::getDefaultFormData($page, '//form');

      self::assertCount(5, $formData);
      self::assertSame('123', $formData['test']);
      self::assertSame('2', $formData['sf']);
      self::assertSame('1', $formData['captcha']);
      self::assertSame('custom text', $formData['text']);
      self::assertSame('16', $formData['sc']);

    }


    /**
     * @expectedException \Exception
     */
    public function testInvalidFormPath() {
      $page = new ElementFinder('<div></div>');
      FormHelper::getDefaultFormData($page, '//form');
    }
  }
