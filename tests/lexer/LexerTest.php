<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Raideer\XmlParser\Lexer;
use Raideer\XmlParser\TokenKind;

final class LexerTest extends TestCase
{
    public function testCanTokenizeSimple(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('<xml></xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $nameToken = $tokens[1];

        $this->assertEquals('xml', $nameToken->value);
        $this->assertEquals(1, $nameToken->offset);

        $lastToken = end($tokens);

        $this->assertEquals(TokenKind::EOF, $lastToken->kind);
    }

    public function testCanTokenizeWithComments(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml><!-- comment --></xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::COMMENT,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $commentToken = $tokens[3];

        $this->assertEquals(' comment ', $commentToken->value);
        $this->assertEquals(9, $commentToken->offset);
    }

    public function testCanTokenizeWithCData(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml><![CDATA[hello]]></xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::CDATA,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $cdataToken = $tokens[3];

        $this->assertEquals('hello', $cdataToken->value);
    }

    public function testCanTokenizeWithEntityRef(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml>&amp;</xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::ENTITY_REF,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $entityRefToken = $tokens[3];

        $this->assertEquals('&amp;', $entityRefToken->value);
    }

    public function testCanTokenizeWithCharRef(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml>&#38;</xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::CHAR_REF,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $charRefToken = $tokens[3];

        $this->assertEquals('&#38;', $charRefToken->value);
    }

    public function testCanTokenizeWithAttributes(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml attr="value"></xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::NAME,
                TokenKind::EQUALS,
                TokenKind::STRING,
                TokenKind::CLOSE,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $attrToken = $tokens[2];

        $this->assertEquals('attr', $attrToken->value);

        $valueToken = $tokens[4];

        $this->assertEquals('"value"', $valueToken->value);
        $this->assertEquals(10, $valueToken->offset);
    }

    public function testCanTokenizeWithText(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml>text</xml>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::TEXT,
                TokenKind::OPEN,
                TokenKind::SLASH,
                TokenKind::NAME,
                TokenKind::CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );

        $textToken = $tokens[3];

        $this->assertEquals('text', $textToken->value);
        $this->assertEquals(5, $textToken->offset);
    }

    public function testCanTokenizeWithSelfClosing(): void
    {
        $lexer = new Raideer\XmlParser\Lexer();
        $tokens = $lexer->tokenize('<xml/>');
        $this->assertIsArray($tokens);

        $tokenKinds = array_map(fn ($token) => $token->kind, $tokens);

        $this->assertEquals(
            [
                TokenKind::OPEN,
                TokenKind::NAME,
                TokenKind::SLASH_CLOSE,
                TokenKind::EOF
            ],
            $tokenKinds
        );
    }
}
