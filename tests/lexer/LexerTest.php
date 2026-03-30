<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Raideer\XmlParser\Lexer;
use Raideer\XmlParser\TokenType;

final class LexerTest extends TestCase
{
    public function testCanTokenizeSimple(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml></xml>');
        $this->assertIsArray($tokens);

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $nameToken = $tokens[1];

        $this->assertEquals('xml', $nameToken->value);
        $this->assertEquals(1, $nameToken->span->start);

        $lastToken = end($tokens);

        $this->assertEquals(TokenType::Eof, $lastToken->type);
    }

    public function testCanTokenizeWithComments(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml><!-- comment --></xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::Comment,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $commentToken = $tokens[3];

        $this->assertEquals(' comment ', $commentToken->value);
        $this->assertEquals(5, $commentToken->span->start);
    }

    public function testCanTokenizeWithCData(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml><![CDATA[hello]]></xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::CData,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $cdataToken = $tokens[3];

        $this->assertEquals('hello', $cdataToken->value);
    }

    public function testCanTokenizeWithEntityRef(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml>&amp;</xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::EntityRef,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $entityRefToken = $tokens[3];

        $this->assertEquals('&amp;', $entityRefToken->value);
    }

    public function testCanTokenizeWithCharRef(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml>&#38;</xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::CharRef,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $charRefToken = $tokens[3];

        $this->assertEquals('&#38;', $charRefToken->value);
    }

    public function testCanTokenizeWithAttributes(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml attr="value"></xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Name,
                TokenType::Equals,
                TokenType::String,
                TokenType::Close,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $attrToken = $tokens[2];

        $this->assertEquals('attr', $attrToken->value);

        $valueToken = $tokens[4];

        $this->assertEquals('value', $valueToken->value);
        $this->assertEquals('"value"', $valueToken->rawValue);
        $this->assertEquals(10, $valueToken->span->start);
    }

    public function testCanTokenizeWithText(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml>text</xml>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::Close,
                TokenType::Text,
                TokenType::Open,
                TokenType::Slash,
                TokenType::Name,
                TokenType::Close,
                TokenType::Eof,
            ],
            $tokenTypes,
        );

        $textToken = $tokens[3];

        $this->assertEquals('text', $textToken->value);
        $this->assertEquals(5, $textToken->span->start);
    }

    public function testCanTokenizeWithSelfClosing(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll('<xml/>');

        $tokenTypes = array_map(fn ($token) => $token->type, $tokens);

        $this->assertEquals(
            [
                TokenType::Open,
                TokenType::Name,
                TokenType::SlashClose,
                TokenType::Eof,
            ],
            $tokenTypes,
        );
    }

    public function testGeneratorYieldsTokensLazily(): void
    {
        $lexer = new Lexer();
        $count = 0;

        foreach ($lexer->tokenize('<root><child/></root>') as $token) {
            $count++;
            if ($count === 3) {
                break; // Can break early from generator
            }
        }

        $this->assertEquals(3, $count);
    }

    public function testTokensHaveCorrectLineAndColumn(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenizeAll("<root>\n  <child/>\n</root>");

        // <root>
        $this->assertEquals(1, $tokens[0]->span->line);
        $this->assertEquals(1, $tokens[0]->span->column);

        // Find the <child/> open token — after <root>\n  there are tokens for Open, Name, Close, whitespace
        // Tokens: Open(<), Name(root), Close(>), SeaWhitespace(\n  ), Open(<), Name(child), SlashClose(/>), SeaWhitespace(\n), Open(<), Slash(/), Name(root), Close(>), Eof
        $childOpen = $tokens[4]; // Open token for <child
        $this->assertEquals(2, $childOpen->span->line);
        $this->assertEquals(3, $childOpen->span->column);
    }
}
