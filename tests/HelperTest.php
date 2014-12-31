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
          <select name="st">
          <option value="1">1</option>
          <option value="2" selected="selected">2</option>
          </select>
        </form>
        </div>
      
      ';
      $page = new \Xparse\ElementFinder\ElementFinder($html);
      $formData = \Xparse\ElementFinder\Helper::getDefaultFormData($page, '//form');
      $this->assertCount(2, $formData);
      $this->assertEquals(123, $formData['test']);
      $this->assertEquals(2, $formData['st']);

    }
  }