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
        </form>
        </div>
      
      ';
      $page = new \Xparse\ElementFinder\ElementFinder($html);
      $formData = \Xparse\ElementFinder\Helper::getDefaultFormData($page, '//form');
      $this->assertCount(1, $formData);
      $this->assertEquals(123, $formData['test']);

    }
  }