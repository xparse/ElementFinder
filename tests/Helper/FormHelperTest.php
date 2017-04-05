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
      <!DOCTYPE html>
      <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>Form Example</title>
        </head>
        <body>
          <h2>Form Example</h2>
          <form method="post">
            <label>
              Name:
              <input type="text" name="name" value="John">
            </label>
            <br><br>
            <label>
              E-mail:
              <input type="text" name="email" value="john.doe@gmail.com">
            </label>
            <br><br>
            <label>
              Website:
              <input type="text" name="website" value="johndoe.com">
            </label>
            <br><br>
            <label>
              Comment:
              <textarea name="comment" rows="5" cols="40">Enter you comment here</textarea>
            </label>
            <br><br>
      
            <label>
              Gender:
              <input type="radio" name="gender" value="female">
              <input type="radio" name="gender" checked value="male">Male
            </label>
            <br><br>
            <label>
              Select car
              <select name="carlist" multiple>
                <option value="volvo">Volvo</option>
                <option selected value="saab">Saab</option>
                <option value="opel">Opel</option>
                <option selected value="audi">Audi</option>
              </select>
            </label>
      
            <input type="checkbox" name="captcha" checked="checked" value="1"/>
      
            <input type="submit" name="submit" value="Submit">
          </form>
        </body>
      </html>
      ';

      $formData = (new FormHelper(new ElementFinder($html)))->getFormData('//form');

      self::assertCount(8, $formData);
      self::assertSame('Enter you comment here', $formData['comment']);
      self::assertSame('male', $formData['gender']);
      self::assertSame('1', $formData['captcha']);
      self::assertSame('John', $formData['name']);
      self::assertSame('john.doe@gmail.com', $formData['email']);
      self::assertSame('johndoe.com', $formData['website']);
      self::assertSame('saab,audi', $formData['carlist']);
      self::assertSame('Submit', $formData['submit']);

    }


    /**
     * @expectedException \Exception
     */
    public function testInvalidFormPath() {
      $page = new ElementFinder('<div></div>');
      (new FormHelper($page))->getFormData('//form');
    }
  }
