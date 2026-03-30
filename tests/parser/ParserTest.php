<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Raideer\XmlParser\Parser;

final class ParserTest extends TestCase
{
    public function testParseSimpleElement(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root></root>');

        $this->assertFalse($result->hasErrors());

        $root = $result->document->getRootElement();
        $this->assertNotNull($root);
        $this->assertEquals('root', $root->getName());
    }

    public function testParseWithAttributes(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root attr="val"></root>');

        $this->assertFalse($result->hasErrors());

        $root = $result->document->getRootElement();
        $attrs = $root->getAttributes();
        $this->assertCount(1, $attrs);
        $this->assertEquals('attr', $attrs[0]->getName());
        $this->assertEquals('val', $attrs[0]->getValue());
        $this->assertEquals('"val"', $attrs[0]->getFullValue());
    }

    public function testParseWithContent(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root>text</root>');

        $this->assertFalse($result->hasErrors());

        $root = $result->document->getRootElement();
        $content = $root->getContent();
        $this->assertNotNull($content);

        $charData = $content->getCharData();
        $this->assertCount(1, $charData);
        $this->assertEquals('text', $charData[0]->getText());
    }

    public function testParseWithNestedElements(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root><child>text</child></root>');

        $this->assertFalse($result->hasErrors());

        $root = $result->document->getRootElement();
        $content = $root->getContent();
        $elements = $content->getElements();
        $this->assertCount(1, $elements);
        $this->assertEquals('child', $elements[0]->getName());
    }

    public function testParseSelfClosingElement(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root/>');

        $this->assertFalse($result->hasErrors());

        $root = $result->document->getRootElement();
        $this->assertEquals('root', $root->getName());
        $this->assertNull($root->getContent());
    }

    public function testParseWithProlog(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<?xml version="1.0"?><root/>');

        $this->assertFalse($result->hasErrors());

        $prolog = $result->document->getProlog();
        $this->assertNotNull($prolog);

        $attrs = $prolog->getAttributes();
        $this->assertCount(1, $attrs);
        $this->assertEquals('version', $attrs[0]->getName());
        $this->assertEquals('1.0', $attrs[0]->getValue());
    }

    public function testParseResultCollectsErrors(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root><unclosed>');

        $this->assertTrue($result->hasErrors());
        $this->assertNotEmpty($result->errors);
        $this->assertNotNull($result->document);
    }

    public function testParseWithEntityRef(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root>&amp;</root>');

        $this->assertFalse($result->hasErrors());

        $content = $result->document->getRootElement()->getContent();
        $refs = $content->getReferences();
        $this->assertCount(1, $refs);
        $this->assertEquals('&amp;', $refs[0]->getEntityRef());
    }

    public function testParseWithCharRef(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root>&#38;</root>');

        $this->assertFalse($result->hasErrors());

        $content = $result->document->getRootElement()->getContent();
        $refs = $content->getReferences();
        $this->assertCount(1, $refs);
        $this->assertEquals('&#38;', $refs[0]->getCharRef());
    }

    public function testParseWithComment(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root><!-- hello --></root>');

        $this->assertFalse($result->hasErrors());

        $content = $result->document->getRootElement()->getContent();
        $comments = $content->getComments();
        $this->assertCount(1, $comments);
        $this->assertEquals(' hello ', $comments[0]);
    }

    public function testParseWithCData(): void
    {
        $parser = new Parser();
        $result = $parser->parse('<root><![CDATA[data]]></root>');

        $this->assertFalse($result->hasErrors());

        $content = $result->document->getRootElement()->getContent();
        $cdata = $content->getCData();
        $this->assertCount(1, $cdata);
        $this->assertEquals('data', $cdata[0]);
    }
}
