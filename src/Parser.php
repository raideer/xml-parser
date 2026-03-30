<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

final class Parser
{
    /** @var Token[] */
    private array $tokens;
    private int $pos;
    /** @var ParseError[] */
    private array $errors;
    private string $input;

    public function parse(string $xml): ParseResult
    {
        $this->input = $xml;
        $this->tokens = (new Lexer())->tokenizeAll($xml);
        $this->pos = 0;
        $this->errors = [];

        $document = $this->parseDocument();

        return new ParseResult($document, $this->errors);
    }

    private function current(): Token
    {
        return $this->tokens[$this->pos] ?? $this->tokens[count($this->tokens) - 1];
    }

    private function advance(): Token
    {
        $token = $this->current();
        if ($this->pos < count($this->tokens) - 1) {
            $this->pos++;
        }
        return $token;
    }

    private function check(TokenType ...$types): bool
    {
        return $this->current()->is(...$types);
    }

    private function match(TokenType ...$types): ?Token
    {
        if ($this->current()->is(...$types)) {
            return $this->advance();
        }

        return null;
    }

    private function expect(TokenType $type): Token
    {
        if ($this->current()->is($type)) {
            return $this->advance();
        }

        $current = $this->current();
        $this->errors[] = new ParseError(
            sprintf("Expected '%s', got '%s'", $type->value, $current->type->value),
            $current->span,
        );

        return new Token(
            TokenType::Missing,
            '',
            '',
            $current->span,
        );
    }

    private function lookahead(TokenType ...$types): bool
    {
        $startPos = $this->pos;
        $succeeded = true;

        foreach ($types as $type) {
            $pos = $startPos + 1;
            if ($pos >= count($this->tokens) || !$this->tokens[$pos]->is($type)) {
                $succeeded = false;
                break;
            }
            $startPos = $pos;
        }

        return $succeeded;
    }

    private function synchronize(): void
    {
        while (!$this->check(TokenType::Open, TokenType::Close, TokenType::Eof)) {
            $this->advance();
        }
    }

    private function parseDocument(): Node\Document
    {
        $document = new Node\Document();

        $document->addChildren(
            $this->parseProlog(),
            $this->parseMisc(),
            $this->parseElement(),
            $this->parseMisc(),
        );

        $document->addChild(
            $this->match(TokenType::Eof),
        );

        return $document;
    }

    private function parseProlog(): ?Node\Prolog
    {
        if (!$this->check(TokenType::XmlDeclOpen)) {
            return null;
        }

        $prolog = new Node\Prolog();

        $prolog->addChild($this->expect(TokenType::XmlDeclOpen));

        while ($attribute = $this->parseAttribute()) {
            $prolog->addChild($attribute);
        }

        $prolog->addChild($this->expect(TokenType::SpecialClose));

        return $prolog;
    }

    private function parseMisc(): ?Node\Misc
    {
        $tokens = [];

        while ($token = $this->match(TokenType::Comment, TokenType::ProcessingInstruction, TokenType::SeaWhitespace)) {
            $tokens[] = $token;
        }

        if (count($tokens) === 0) {
            return null;
        }

        $misc = new Node\Misc();
        $misc->addChildren(...$tokens);
        return $misc;
    }

    private function parseElement(): ?Node\Element
    {
        if (!$this->check(TokenType::Open) || $this->lookahead(TokenType::Slash, TokenType::Name)) {
            return null;
        }

        $element = new Node\Element();

        $element->addChildren(
            $this->expect(TokenType::Open),
            $this->expect(TokenType::Name),
        );

        while ($attribute = $this->parseAttribute()) {
            $element->addChild($attribute);
        }

        // <element>...</element>
        if ($this->check(TokenType::Close)) {
            $element->addChild($this->expect(TokenType::Close));
            $element->addChild($this->parseContent());

            $element->addChildren(
                $this->expect(TokenType::Open),
                $this->expect(TokenType::Slash),
                $this->expect(TokenType::Name),
                $this->expect(TokenType::Close),
            );
        } else {
            // <element />
            $element->addChild($this->expect(TokenType::SlashClose));
        }

        return $element;
    }

    private function parseContent(): ?Node\Content
    {
        $content = new Node\Content();

        $content->addChild($this->parseCharData());

        while ($this->parseContentInner($content)) {
            // Keep parsing
        }

        $content->addChild($this->parseCharData());

        if (count($content->getChildren()) === 0) {
            return null;
        }

        return $content;
    }

    private function parseContentInner(Node\Content $content): bool
    {
        if ($this->parseContentMidSection($content)) {
            $content->addChild($this->parseCharData());
            return true;
        }

        return false;
    }

    private function parseContentMidSection(Node\Content $content): bool
    {
        if ($element = $this->parseElement()) {
            $content->addChild($element);
            return true;
        }

        if ($reference = $this->parseReference()) {
            $content->addChild($reference);
            return true;
        }

        if ($token = $this->match(TokenType::CData, TokenType::ProcessingInstruction, TokenType::Comment)) {
            $content->addChild($token);
            return true;
        }

        return false;
    }

    private function parseReference(): ?Node\Reference
    {
        $tokens = [];

        while ($token = $this->match(TokenType::EntityRef, TokenType::CharRef)) {
            $tokens[] = $token;
        }

        if (count($tokens) === 0) {
            return null;
        }

        $reference = new Node\Reference();
        $reference->addChildren(...$tokens);
        return $reference;
    }

    private function parseCharData(): ?Node\CharData
    {
        $tokens = [];

        while ($token = $this->match(TokenType::Text, TokenType::SeaWhitespace)) {
            $tokens[] = $token;
        }

        if (count($tokens) === 0) {
            return null;
        }

        $charData = new Node\CharData();
        $charData->addChildren(...$tokens);
        return $charData;
    }

    private function parseAttribute(): ?Node\Attribute
    {
        if (!$this->check(TokenType::Name)) {
            return null;
        }

        $attribute = new Node\Attribute();

        $attribute->addChildren(
            $this->expect(TokenType::Name),
            $this->expect(TokenType::Equals),
            $this->expect(TokenType::String),
        );

        return $attribute;
    }
}
