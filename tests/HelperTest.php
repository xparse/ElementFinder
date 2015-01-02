<?

  namespace Xparse\ElementFinder\Test;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/30/14
   */
  class HelperTest extends \Xparse\ElementFinder\Test\Main {


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
      $page = new \Xparse\ElementFinder\ElementFinder($html);
      $formData = \Xparse\ElementFinder\Helper::getDefaultFormData($page, '//form');
      $this->assertCount(5, $formData);
      $this->assertEquals(123, $formData['test']);
      $this->assertEquals(2, $formData['sf']);
      $this->assertEquals(1, $formData['captcha']);
      $this->assertEquals('custom text', $formData['text']);
      $this->assertEquals(16, $formData['sc']);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormPath() {
      $page = new \Xparse\ElementFinder\ElementFinder('<div></div>');
      \Xparse\ElementFinder\Helper::getDefaultFormData($page, '//form');
    }

    public function testEncode() {

      $data = array(
        '<' => '&#60;',
        '> &#s' => '&#62; &#s',
        '&  ' => '&#38;  ',
        '¢' => '&#162;',
        '£' => '&#163;',
        '¥' => '&#165;',
        '€' => '&#8364;',
        '©' => '&#169;',
        'Data ®' => 'Data &#174;',
      );

      foreach ($data as $expect => $string) {
        $output = \Xparse\ElementFinder\Helper::safeEncodeStr($string);
        $this->assertEquals($expect, $output);
      }

    }

  }