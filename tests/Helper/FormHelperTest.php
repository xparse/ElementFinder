<?php

declare(strict_types=1);

namespace Tests\Xparse\ElementFinder\Helper;

use Exception;
use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\ElementFinder;
use Xparse\ElementFinder\Helper\FormHelper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
final class FormHelperTest extends TestCase
{
    public function testFormData(): void
    {
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
              <select name="carlist[]" multiple>
                <option value="volvo">Volvo</option>
                <option selected value="saab">Saab</option>
                <option value="opel">Opel</option>
                <option selected value="audi">Audi</option>
              </select>
            </label>
            <label>
              Select motorbike
              <select name="motolist" multiple>
                <option value="yamaha">Yamaha</option>
                <option value="kawasaki">Kawasaki</option>
                <option selected value="honda">Honda</option>
                <option selected value="bmw">BMW</option>
              </select>
            </label>
            <label>
              Select age
              <select name="age">
                <option value="20">20-30</option>
                <option selected value="30">31-40</option>
                <option value="40">41-50</option>
                <option value="50">51-100</option>
              </select>
            </label>
      
            <input type="checkbox" name="captcha" checked="checked" value="1"/>
      
            <input type="submit" name="submit" value="Submit">
          </form>
        </body>
      </html>
      ';

        $formData = (new FormHelper(new ElementFinder($html)))->getFormData('//form');


        self::assertSame([
            'comment' => 'Enter you comment here',
            'gender' => 'male',
            'captcha' => '1',
            'name' => 'John',
            'email' => 'john.doe@gmail.com',
            'website' => 'johndoe.com',
            'submit' => 'Submit',
            'age' => '30',
            'carlist' => [
                0 => 'saab',
                1 => 'audi',
            ],
            'motolist' => 'bmw',
        ], $formData);
    }


    public function testInvalidFormPath(): void
    {
        $page = new ElementFinder('<div></div>');
        $helper = (new FormHelper($page));
        $this->expectException(Exception::class);
        /** @noinspection UnusedFunctionResultInspection */
        $helper->getFormData('//form');
    }
}
