<?php

  namespace Xparse\ElementFinder\Test;

  use Xparse\ElementFinder\ElementFinder;

  /**
   *
   * @package Xparse\ElementFinder\Test
   */
  class ElementFinderTest extends \Xparse\ElementFinder\Test\Main {

    public function testLoad() {
      $html = $this->getHtmlTestObject();
      $this->assertContains('<title>test doc</title>', (string) $html);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType() {
      new ElementFinder("", "df");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadEmptyDoc() {
      new ElementFinder("");
    }

    /**
     *
     */
    public function testLoadDocumentWithZero() {
      $elementFinder = new ElementFinder("   0 ");
      $this->assertContains('0', (string) $elementFinder);
    }

    public function testNodeList() {

      $html = $this->getHtmlTestObject();
      $spanNodes = $html->node('//span');

      $this->assertInstanceOf('\DOMNodeList', $spanNodes);

      $this->assertEquals(4, $spanNodes->length);
    }

    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $links = $html->attribute("//a/@href");

      $this->assertCount(1, $links);

      foreach ($html->html("//a") as $htmlString) {
        $this->assertTrue(is_string($htmlString));
      }

      $firstLink = $html->html("//a", true)->item(0);

      $this->assertContains('<a href="http://funivan.com/" title="my blog">link</a>', (string) $firstLink);
    }


    public function testObjects() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object("//span");

      $this->assertCount(4, $spanItems);

      /** @var ElementFinder $span */
      foreach ($spanItems->extractItems(0, 3) as $index => $span) {
        $itemHtml = $span->html('//i')->item(0);

        $this->assertEquals('r', trim($itemHtml));

      }

      $html->remove('//span[2]');

      $spanItems = $html->html("//span");
      $this->assertCount(3, $spanItems);

      $html->remove('//span[@class]');

      $spanItems = $html->html("//span");
      $this->assertCount(1, $spanItems);

    }


    public function testDelete() {
      $html = $this->getHtmlTestObject();

      $title = $html->value('//title')->item(0);
      $this->assertEquals('test doc', $title);

      $html->remove('//title');

      $title = $html->value('//title')->item(0);
      $this->assertEmpty($title);

    }


    public function testHtmlSelector() {
      $html = $this->getHtmlTestObject();
      $stringCollection = $html->html('//td');

      $this->assertCount(1, $stringCollection);
      $this->assertEquals('', $stringCollection->item(10));

      $title = $stringCollection->item(0);
      $this->assertEquals('custom <a href="http://funivan.com/" title="my blog">link</a>', (string) $title);

      $title = $html->html('//td/@df')->item(0);
      $this->assertEmpty((string) $title);
    }

    public function testGetNodeItems() {
      $html = $this->getHtmlTestObject();
      $group = $html->getNodeItems('//span', array(
        'b' => '//b[1]',
        'i' => '//o',
        'if' => '//i/@df',
      ));

      $this->assertCount(4, $group);

      $this->assertNotEmpty($group[0]['b']);

      foreach ($group as $i => $item) {
        $this->assertEmpty($item['if']);
      }

    }

    public function testRegexpReplace() {
      $html = $this->getHtmlDataObject();
      $html->replace('!-!', '+');

      $this->assertContains('45+12+16', (string) $html);

      $phones = $html->html('//*[@id="tels"]');

      $this->assertCount(1, $phones);

      $phones->replace('![\+\s]!');

      $this->assertContains('451216', $phones->getFirst());

    }


    public function testMatch() {

      $html = $this->getHtmlDataObject();
      $regex = '!([\d-]+)[<|\n]{1}!';

      $phones = $html->match($regex);
      $this->assertCount(2, $phones);

      $phones = $html->match($regex, 0);
      $this->assertCount(2, $phones);
      $this->assertContains('<', $phones[0]);
      $this->assertContains("\n", $phones[1]);

      $phones = $html->match($regex, 4);
      $this->assertCount(0, $phones);

    }

    public function testObjectWithInnerHtml() {

      $html = $this->getHtmlTestObject();

      # inner 
      $spanItems = $html->object('//span');
      $this->assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      $this->assertNotContains('<span class="span-1">', (string) $firstItem);
      $this->assertContains('<b>1 </b>', (string) $firstItem);
    }

    public function testReplaceAllData() {
      $html = $this->getHtmlTestObject();
      $html->replace('!.*!');
    }

    public function testInitClassWithInvalidContent() {
      $internalErrors = libxml_use_internal_errors(true);
      new ElementFinder('
        <!DOCTYPE html>
        <html>
          <head></head>
          <body>
            <span></span></span>
          </body>
        </html>
'
      );

      $errors = libxml_get_errors();
      libxml_clear_errors();
      libxml_use_internal_errors($internalErrors);
      $this->assertCount(1, $errors);
      $this->assertContains("Unexpected end tag : span\n", $errors[0]->message);

    }


    public function testInitClassWithValidContent() {
      $internalErrors = libxml_use_internal_errors(true);
      $this->getHtmlDataObject();

      $errors = libxml_get_errors();
      libxml_clear_errors();
      libxml_use_internal_errors($internalErrors);
      $this->assertCount(0, $errors);

    }

    public function testGetObjectWithEmptyHtml() {
      $page = new ElementFinder("<div></div><div><a>df</a></div>");
      $objects = $page->object('//div');

      $this->assertEmpty((string) $objects->item(0));
      $this->assertContains('data-document-is-empty', $objects[0]->html('/')->item(0));

      $this->assertNotEmpty((string) $objects->item(1));
      $linkText = $objects->item(1)->value('//a')->item(0);
      $this->assertEquals('df', $linkText);

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentType() {
      new ElementFinder("<div></div>", false);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentOptions() {
      new ElementFinder("<div></div>", null, 'test');
    }

    /**
     *
     */
    public function testValidDocumentType() {
      $document = new ElementFinder("<xml><list>123</list></xml>", ElementFinder::DOCUMENT_XML);
      $this->assertContains('<list>123</list>', (string) $document);
    }

  } 