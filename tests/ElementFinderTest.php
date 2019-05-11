<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder;

use PHPUnit\Framework\TestCase;
use Test\Xparse\ElementFinder\Dummy\ItemsByClassExpressionTranslator;
use Xparse\ElementFinder\DomNodeListAction\RemoveNodes;
use Xparse\ElementFinder\ElementFinder;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
final class ElementFinderTest extends TestCase
{
    public function testLoad(): void
    {
        $html = $this->getHtmlTestObject();
        self::assertContains('<title>test doc</title>', $html->content('.')->first());
    }


    /**
     * @expectedException \Exception
     */
    public function testInvalidType(): void
    {
        new ElementFinder('', -1);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadEmptyDoc(): void
    {
        new ElementFinder('');
    }


    /**
     *
     */
    public function testLoadDocumentWithZero(): void
    {
        self::assertSame(
            '<body><p>0 </p></body>',
            (new ElementFinder('   0 '))->content('.')->first()
        );
    }


    public function testAttributes(): void
    {
        $html = $this->getHtmlTestObject();

        $links = $html->value('//a/@href');

        self::assertCount(1, $links);

        foreach ($html->content('//a') as $htmlString) {
            self::assertTrue(is_string($htmlString));
        }

        self::assertContains(
            '<a href="http://funivan.com/" title="my blog">link</a>',
            $html->content('//a', true)->first()
        );
    }


    public function testObjects(): void
    {
        $html = $this->getHtmlTestObject();

        $spanItems = $html->object('//span');

        self::assertCount(4, $spanItems);

        $html = $html->remove('//span[2]');

        $spanItems = $html->content('//span');
        self::assertCount(3, $spanItems);

        $html = $html->remove('//span[@class]');

        $spanItems = $html->content('//span');
        self::assertCount(1, $spanItems);
    }


    public function testModify(): void
    {
        $page = new ElementFinder('<html><span>user</span></html>');
        self::assertCount(1, $page->value('//span'));
        $cleanPage = $page->modify('//span', new RemoveNodes());
        self::assertCount(1, $page->value('//span'));
        self::assertCount(0, $cleanPage->value('//span'));
    }

    public function testObjectWithOuterHtml(): void
    {
        $spanItems = $this->getHtmlTestObject()->object('//span', true);
        self::assertCount(4, $spanItems);
        self::assertContains(
            '<span class="span-1">',
            $spanItems->get(0)->content('.')->first()
        );
    }


    public function testDeleteNode(): void
    {
        $html = $this->getHtmlTestObject();

        /** @noinspection UnusedFunctionResultInspection */
        $html->remove('//title');
        $title = $html->value('//title')->first();
        self::assertNotNull($title);

        $html = $html->remove('//title');
        $title = $html->value('//title')->first();
        self::assertNull($title);
    }


    public function testDeleteAttribute(): void
    {
        $html = $this->getHtmlTestObject();

        /** @noinspection UnusedFunctionResultInspection */
        $html->remove('//a/@title');
        $title = $html->value('//a/@title')->first();
        self::assertNotNull($title);

        $html = $html->remove('//a/@title');
        $title = $html->value('//a/@title')->first();
        self::assertNull($title);
    }


    public function testHtmlSelector(): void
    {
        $html = $this->getHtmlTestObject();
        $stringCollection = $html->content('//td');

        self::assertCount(1, $stringCollection);
        self::assertEquals('', $stringCollection->get(10));

        self::assertEquals(
            'custom <a href="http://funivan.com/" title="my blog">link</a>',
            $stringCollection->get(0)
        );
        self::assertEquals('', $html->content('//td/@df')->get(0));
    }


    public function testMatch(): void
    {
        $html = $this->getHtmlDataObject();
        $regex = '!([\d-]+)[<|\n]!';

        $phones = $html->content('.')->match($regex);
        self::assertCount(2, $phones);

        $phones = $html->content('.')->match($regex, 0);
        self::assertCount(2, $phones);
        self::assertContains('<', $phones->get(0));
        self::assertContains("\n", $phones->get(1));

        $phones = $html->content('.')->match($regex, 4);
        self::assertCount(0, $phones);
    }


    public function testMatchWithEmptyElements(): void
    {
        self::assertEmpty(
            $this->getHtmlDataObject()->content('.')->match('!(1233)!')
        );
    }


    public function testObjectWithInnerContent(): void
    {
        # inner
        $spanItems = $this->getHtmlTestObject()->object('//span');
        self::assertCount(4, $spanItems);

        self::assertNotContains('<span class="span-1">', $spanItems->get(0)->content('.')->first());
        self::assertContains('<b>1 </b>', $spanItems->get(0)->content('.')->first());
    }


    /**
     * How would you find all nodes between all H2's?
     *
     * Using Kayessian XPath formula
     *
     * `$ns1[count(.|$ns2) = count($ns2)]`
     *
     * you can select all the nodes that belong both to the node sets $ns1 and $ns2.
     *
     */
    public function testGetAllNodesBetweenSiblings(): void
    {
        $html = new ElementFinder('
        <html>
          <h2>Title1</h2>
            <p>Text 1</p>
          <h2>Title2</h2>
            <p>Text 2</p>
          <h2>Title3</h2>
            <p>Text 3</p>
          <h2>Title4</h2>
            <p>Text 4</p>
          <h2>Title5</h2>
        </html>
      ');
        $ns1 = '//*/h2[1]/following-sibling::p';
        $ns2 = '//*/h2[count(//h2)]/preceding-sibling::p';
        $result = $html->value($ns1 . '[count(.|' . $ns2 . ') = count(' . $ns2 . ')]')->all();

        self::assertCount(4, $result);
        self::assertEquals($result[0], 'Text 1');
        self::assertEquals($result[3], 'Text 4');
    }


    public function testInitClassWithInvalidContent(): void
    {
        $elementFinder = new ElementFinder('
        <!DOCTYPE html>
        <html>
          <head><title></title></head>
          <body>
            <span></span></span>
          </body>
        </html>
      ');

        $errors = $elementFinder->getLoadErrors();

        self::assertCount(1, $errors);
        self::assertContains("Unexpected end tag : span\n", $errors[0]->message);
    }


    public function testInitClassWithValidContent(): void
    {
        $errors = $this->getHtmlDataObject()->getLoadErrors();
        self::assertCount(0, $errors);
    }


    public function testGetObjectWithEmptyHtml(): void
    {
        $objects = (new ElementFinder('<div></div><div><a>df</a></div>'))->object('//div');

        self::assertEmpty($objects->get(0)->content('.')->first());
        self::assertContains('data-document-is-empty', $objects->get(0)->content('/')->get(0));

        self::assertNotEmpty($objects->get(1)->content('.')->first());
        $linkText = $objects->get(1)->value('//a')->get(0);
        self::assertEquals('df', $linkText);
    }


    /**
     *
     */
    public function testValidDocumentType(): void
    {
        $document = new ElementFinder('<xml><list>123</list></xml>', ElementFinder::DOCUMENT_XML);
        self::assertContains('<list>123</list>', $document->content('.')->first());
    }


    public function testFetchTextNode(): void
    {
        $html = new ElementFinder('
        <div>
          <ul>
            <li><b>param1:</b>t1<span>or</span>t2</li>
            <li><b>param2:</b>other</li>
            <li>param3: new</li>
          </ul>
        </div>
      ');


        $firstTextNodes = $html->value('//b/following-sibling::text()[1]')->all();

        self::assertEquals([
            't1', 'other',
        ], $firstTextNodes);


        $allFollowingSiblingTextNodes = $html->value('//b/following-sibling::text()')->all();

        self::assertEquals([
            't1', 't2', 'other',
        ], $allFollowingSiblingTextNodes);
    }


    public function testKeyValue(): void
    {
        $html = new ElementFinder('
        <table>
          <tbody>
          <tr>
            <td>Year</td>
            <td>2016</td>
          </tr>
          <tr>
            <td>Make</td>
            <td>CAT</td>
          </tr>
          <tr>
            <td>Model</td>
            <td>560G</td>
          </tr>
          </tbody>
        </table>
      ');
        $values = $html->keyValue('//table//td[1]', '//table//td[2]');

        self::assertEquals([
            'Year' => '2016',
            'Make' => 'CAT',
            'Model' => '560G',
        ], $values);
    }


    /**
     * @expectedException \Exception
     */
    public function testKeyValueFail(): void
    {
        $html = new ElementFinder('
        <table>
          <tbody>
          <tr>
            <td>Year</td>
            <td>2016</td>
          </tr>
          <tr>
            <td>Make</td>
            <td>CAT</td>
          </tr>
          <tr>
            <td>560G</td>
          </tr>
          </tbody>
        </table>
      ');
        /** @noinspection UnusedFunctionResultInspection */
        $html->keyValue('//table//td[1]', '//table//td[2]');
    }


    public function testXmlData(): void
    {
        $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);
        $foods = $xml->object('//food');

        self::assertCount(5, $foods);

        $xml = $xml->remove('//food[3]');

        $foods = $xml->object('//food');
        self::assertCount(4, $foods);

        self::assertEquals('$5.95', $xml->value('//food[1]/price/@value')->first());

        self::assertEquals(950, $xml->value('//food/calories')->last());

        self::assertEquals(900, $xml->content('//food[2]/calories')->first());

        self::assertEquals('5.95 USD', $xml->content('.')->match('!<price value="([^"]+)"!iu')->replace('!^\\$(.+)!iu', '$1 USD')->first());
    }


    public function testXmlRootNode(): void
    {
        $food = (new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML))->object('//food')->get(2);
        self::assertEquals(900, (int) $food->value('/root/calories')->first());
    }


    public function testLoadXmlWithoutErrors(): void
    {
        $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);

        self::assertCount(0, $xml->getLoadErrors());
    }


    public function testLoadXmlWithErrors(): void
    {
        $errors = (new ElementFinder($this->getInvalidXml(), ElementFinder::DOCUMENT_XML))->getLoadErrors();

        self::assertCount(1, $errors);
        self::assertContains('Opening and ending tag mismatch: from', $errors[0]->message);
    }


    public function testXmlRootNodes(): void
    {
        $xml = new ElementFinder($this->getInvalidRootNodesXml(), ElementFinder::DOCUMENT_XML);
        $errors = $xml->getLoadErrors();

        self::assertCount(1, $errors);
        self::assertContains('Extra content at the end of the document', $errors[0]->message);
    }


    public function testShareExpressionTranslator(): void
    {
        $page = new ElementFinder('
          <div class="node"> 
            <a href="#" class="link">test0</a>
          </div>
          <div class="node"> 
            <a href="#" class="link">test1</a>
          </div>
          <div class="node"> 
            <a href="#" class="link">test2</a>
          </div>
', null, new ItemsByClassExpressionTranslator());

        $expression = 'node';

        $objects = $page->object($expression);
        self::assertCount(3, $objects);

        foreach ($objects as $index => $object) {
            $link = $object->content('link');
            self::assertCount(1, $link);
            self::assertEquals('test' . $index, $link->first());
        }
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlTestObject(): ElementFinder
    {
        return $this->initFromFile('test.html');
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlDataObject(): ElementFinder
    {
        return $this->initFromFile('data.html');
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getNodeItemsHtmlObject(): ElementFinder
    {
        return $this->initFromFile('node-items.html');
    }


    final public function testElement(): void
    {
        $page = new ElementFinder('<div><span title="Hello">sdf</span></div>');
        $first = $page->element('//span')->first();
        if ($first !== null) {
            /** @noinspection UnusedFunctionResultInspection */
            $first->setAttribute('title', 'Changed');
            self::assertSame('Changed', $first->getAttribute('title'));
        } else {
            self::fail('Cant find first title');
        }

        $second = $page->element('//span')->first();
        if ($first !== null) {
            self::assertSame('Hello', $second->getAttribute('title'));
        } else {
            self::fail('Can find modified title');
        }
    }


    /**
     * @return string
     */
    private function getDemoDataDirectoryPath(): string
    {
        return __DIR__ . '/demo-data/';
    }


    private function initFromFile(string $file): ElementFinder
    {
        $fileData = file_get_contents($this->getDemoDataDirectoryPath() . DIRECTORY_SEPARATOR . $file);
        return new \Xparse\ElementFinder\ElementFinder($fileData);
    }


    /**
     * @return string
     */
    private function getInvalidRootNodesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
      <note>
        <to>Tove</to>
        <from>Jani</from>
          <heading>Reminder</heading>
          <body>Don\'t forget me this weekend!</body>
      </note>
      <note>
        <to>John</to>
        <from>Doe</from>
          <heading>Reminder 2</heading>
          <body>Don\'t forget me this month!</body>
      </note>
      ';
    }


    /**
     * @return string
     */
    private function getInvalidXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
      <note>
        <to>Tove</to>
        <from>Jani</Ffrom>
          <heading>Reminder</heading>
          <body>Don\'t forget me this weekend!</body>
      </note>
      ';
    }


    /**
     * @return string
     */
    private function getValidXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
      <breakfast_menu>
          <food>
              <name>Belgian Waffles</name>
              <price value="$5.95"/>
              <description>Two of our famous Belgian Waffles with plenty of real maple syrup</description>
              <calories>650</calories>
          </food>
          <food>
              <name>Strawberry Belgian Waffles</name>
              <price value="$7.95"/>
              <description>Light Belgian waffles covered with strawberries and whipped cream</description>
              <calories>900</calories>
          </food>
          <food>
              <name>Berry-Berry Belgian Waffles</name>
              <price value="$8.95"/>
              <description>Light Belgian waffles covered with an assortment of fresh berries and whipped cream</description>
              <calories>900</calories>
          </food>
          <food>
              <name>French Toast</name>
              <price value="$4.50"/>
              <description>Thick slices made from our homemade sourdough bread</description>
              <calories>600</calories>
          </food>
          <food>
              <name>Homestyle Breakfast</name>
              <price value="$6.95"/>
              <description>Two eggs, bacon or sausage, toast, and our ever-popular hash browns</description>
              <calories>950</calories>
          </food>
      </breakfast_menu>
      ';
    }
}
