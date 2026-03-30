<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Raideer\XmlParser\Xml;
use Raideer\XmlParser\ParseException;
use Raideer\XmlParser\Node;

final class XmlTest extends TestCase
{
    public function testParseReturnsDocument(): void
    {
        $document = Xml::parse('<root attr="val"><child>text</child></root>');

        $this->assertInstanceOf(Node\Document::class, $document);

        $root = $document->getRootElement();
        $this->assertEquals('root', $root->getName());
        $this->assertCount(1, $root->getAttributes());
        $this->assertEquals('val', $root->getAttributes()[0]->getValue());

        $content = $root->getContent();
        $elements = $content->getElements();
        $this->assertCount(1, $elements);
        $this->assertEquals('child', $elements[0]->getName());
    }

    public function testParseThrowsOnErrors(): void
    {
        $this->expectException(ParseException::class);
        Xml::parse('<root><unclosed>');
    }

    public function testTryParseReturnsResult(): void
    {
        $result = Xml::tryParse('<root><unclosed>');

        $this->assertTrue($result->hasErrors());
        $this->assertNotNull($result->document);
        $this->assertNotEmpty($result->errors);
    }

    public function testTryParseSuccessful(): void
    {
        $result = Xml::tryParse('<root/>');

        $this->assertFalse($result->hasErrors());
        $this->assertNotNull($result->document);
        $this->assertEquals('root', $result->document->getRootElement()->getName());
    }

    public function testParseExceptionContainsErrors(): void
    {
        try {
            Xml::parse('<root><unclosed>');
            $this->fail('Expected ParseException');
        } catch (ParseException $e) {
            $this->assertNotEmpty($e->errors);
            $this->assertNotEmpty($e->getMessage());
        }
    }

    public function testTokenHasSpanWithCorrectPosition(): void
    {
        $document = Xml::parse('<root/>');

        $root = $document->getRootElement();
        $span = $root->getSpan();

        $this->assertNotNull($span);
        $this->assertEquals(0, $span->start);
        $this->assertEquals(7, $span->end);
        $this->assertEquals(1, $span->line);
        $this->assertEquals(1, $span->column);
    }
}
