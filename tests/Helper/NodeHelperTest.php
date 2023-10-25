<?php

declare(strict_types=1);

namespace Tests\Xparse\ElementFinder\Helper;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\ElementFinder;
use Xparse\ElementFinder\Helper\NodeHelper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
final class NodeHelperTest extends TestCase
{
    public function testHtmlOuterContent(): void
    {
        $originalHtml = "<body><style>
          span {
            font-family: tahoma;
          }
        </style></body>";

        $doc = new \DOMDocument('1.0');
        $doc->loadHTML($originalHtml);
        $body = $doc->getElementsByTagName("body")->item(0);

        self::assertSame(
            $originalHtml,
            trim(NodeHelper::getOuterContent($body, ElementFinder::DOCUMENT_HTML))
        );
    }
}
