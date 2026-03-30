<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

final class Lexer
{
    private const MODE_DEFAULT = 0;
    private const MODE_INSIDE = 1;

    private string $input;
    private int $pos;
    private int $line;
    private int $column;
    private int $length;
    private int $mode;
    /** @var int[] */
    private array $modeStack;

    /**
     * @return \Generator<Token>
     */
    public function tokenize(string $input): \Generator
    {
        $this->input = $input;
        $this->pos = 0;
        $this->line = 1;
        $this->column = 1;
        $this->length = strlen($input);
        $this->mode = self::MODE_DEFAULT;
        $this->modeStack = [];

        while ($this->pos < $this->length) {
            $token = match ($this->mode) {
                self::MODE_DEFAULT => $this->scanDefault(),
                self::MODE_INSIDE => $this->scanInside(),
            };

            if ($token !== null) {
                yield $token;
            }
        }

        yield $this->makeToken(TokenType::Eof, '', '', $this->pos);
    }

    /**
     * @return Token[]
     */
    public function tokenizeAll(string $input): array
    {
        $tokens = [];
        foreach ($this->tokenize($input) as $token) {
            $tokens[] = $token;
        }
        return $tokens;
    }

    private function scanDefault(): ?Token
    {
        return match ($this->input[$this->pos]) {
            '<' => $this->openTag(),
            '&' => $this->reference(),
            default => $this->text(),
        };
    }

    private function scanInside(): ?Token
    {
        $ch = $this->input[$this->pos];

        return match (true) {
            $ch === '>' => $this->closeTag(),
            $ch === '/' && $this->peek(1) === '>' => $this->slashClose(),
            $ch === '?' && $this->peek(1) === '>' => $this->specialClose(),
            $ch === '/' => $this->singleChar(TokenType::Slash),
            $ch === '=' => $this->singleChar(TokenType::Equals),
            $ch === '"', $ch === "'" => $this->scanString($ch),
            $this->isWhitespace($ch) => $this->consumeInsideWhitespace(),
            $this->isNameStartChar($ch) => $this->name(),
            $ch === '<' => $this->skipInvalidOpen(),
            default => $this->singleChar(TokenType::Error),
        };
    }

    private function closeTag(): Token
    {
        $this->popMode();
        return $this->singleChar(TokenType::Close);
    }

    private function slashClose(): Token
    {
        $this->popMode();
        return $this->makeTokenAndAdvance(TokenType::SlashClose, '/>', '/>', 2);
    }

    private function specialClose(): Token
    {
        $this->popMode();
        return $this->makeTokenAndAdvance(TokenType::SpecialClose, '?>', '?>', 2);
    }

    private function consumeInsideWhitespace(): ?Token
    {
        $this->skipWhitespace();
        return null;
    }

    private function skipInvalidOpen(): ?Token
    {
        $this->advance();
        return null;
    }

    private function openTag(): Token
    {
        return match (true) {
            $this->peek(1) === '!' && $this->matchAhead('<!--') => $this->comment(),
            $this->peek(1) === '!' && $this->matchAhead('<![CDATA[') => $this->cdata(),
            $this->peek(1) === '!' => $this->dtd(),
            $this->isXmlDeclStart() => $this->xmlDeclOpen(),
            $this->peek(1) === '?' => $this->processingInstruction(),
            default => $this->regularOpen(),
        };
    }

    private function isXmlDeclStart(): bool
    {
        return $this->matchAhead('<?xml')
            && $this->pos + 5 < $this->length
            && $this->isWhitespace($this->input[$this->pos + 5]);
    }

    private function regularOpen(): Token
    {
        $this->pushMode(self::MODE_INSIDE);
        return $this->singleChar(TokenType::Open);
    }

    private function comment(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advanceBy(4);
        $valueStart = $this->pos;

        while ($this->pos < $this->length) {
            if ($this->input[$this->pos] === '-' && $this->matchAhead('-->')) {
                $value = substr($this->input, $valueStart, $this->pos - $valueStart);
                $this->advanceBy(3);
                $raw = substr($this->input, $start, $this->pos - $start);
                return new Token(TokenType::Comment, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
            }
            $this->advance();
        }

        $value = substr($this->input, $valueStart);
        $raw = substr($this->input, $start);
        return new Token(TokenType::Comment, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function cdata(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advanceBy(9);
        $valueStart = $this->pos;

        while ($this->pos < $this->length) {
            if ($this->input[$this->pos] === ']' && $this->matchAhead(']]>')) {
                $value = substr($this->input, $valueStart, $this->pos - $valueStart);
                $this->advanceBy(3);
                $raw = substr($this->input, $start, $this->pos - $start);
                return new Token(TokenType::CData, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
            }
            $this->advance();
        }

        $value = substr($this->input, $valueStart);
        $raw = substr($this->input, $start);
        return new Token(TokenType::CData, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function dtd(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advanceBy(2);

        while ($this->pos < $this->length && $this->input[$this->pos] !== '>') {
            $this->advance();
        }

        if ($this->pos < $this->length) {
            $this->advance();
        }

        $raw = substr($this->input, $start, $this->pos - $start);
        return new Token(TokenType::Dtd, $raw, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function xmlDeclOpen(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advanceBy(6);

        $raw = substr($this->input, $start, $this->pos - $start);
        $this->pushMode(self::MODE_INSIDE);
        return new Token(TokenType::XmlDeclOpen, $raw, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function processingInstruction(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advanceBy(2);
        $valueStart = $this->pos;

        while ($this->pos < $this->length) {
            if ($this->input[$this->pos] === '?' && $this->peek(1) === '>') {
                $value = substr($this->input, $valueStart, $this->pos - $valueStart);
                $this->advanceBy(2);
                $raw = substr($this->input, $start, $this->pos - $start);
                return new Token(TokenType::ProcessingInstruction, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
            }
            $this->advance();
        }

        $value = substr($this->input, $valueStart);
        $raw = substr($this->input, $start);
        return new Token(TokenType::ProcessingInstruction, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function reference(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advance();

        $isCharRef = false;
        if ($this->pos < $this->length && $this->input[$this->pos] === '#') {
            $isCharRef = true;
            $this->advance();
            if ($this->pos < $this->length && $this->input[$this->pos] === 'x') {
                $this->advance();
            }
        }

        while ($this->pos < $this->length && $this->input[$this->pos] !== ';' && $this->input[$this->pos] !== '<') {
            $this->advance();
        }

        if ($this->pos < $this->length && $this->input[$this->pos] === ';') {
            $this->advance();
        }

        $raw = substr($this->input, $start, $this->pos - $start);
        $type = $isCharRef ? TokenType::CharRef : TokenType::EntityRef;
        return new Token($type, $raw, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function text(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $isWhitespaceOnly = true;

        while ($this->pos < $this->length && $this->input[$this->pos] !== '<' && $this->input[$this->pos] !== '&') {
            if (!$this->isWhitespace($this->input[$this->pos])) {
                $isWhitespaceOnly = false;
            }
            $this->advance();
        }

        $raw = substr($this->input, $start, $this->pos - $start);
        $type = $isWhitespaceOnly ? TokenType::SeaWhitespace : TokenType::Text;
        return new Token($type, $raw, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function name(): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        while ($this->pos < $this->length && $this->isNameChar($this->input[$this->pos])) {
            $this->advance();
        }

        $raw = substr($this->input, $start, $this->pos - $start);
        return new Token(TokenType::Name, $raw, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function scanString(string $quote): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;

        $this->advance();
        $valueStart = $this->pos;

        while ($this->pos < $this->length && $this->input[$this->pos] !== $quote) {
            if ($this->input[$this->pos] === '<' || $this->input[$this->pos] === '>') {
                $value = substr($this->input, $valueStart, $this->pos - $valueStart);
                $raw = substr($this->input, $start, $this->pos - $start);
                return new Token(TokenType::InvalidString, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
            }
            $this->advance();
        }

        if ($this->pos < $this->length) {
            $value = substr($this->input, $valueStart, $this->pos - $valueStart);
            $this->advance();
            $raw = substr($this->input, $start, $this->pos - $start);
            return new Token(TokenType::String, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
        }

        $value = substr($this->input, $valueStart);
        $raw = substr($this->input, $start);
        return new Token(TokenType::InvalidString, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function singleChar(TokenType $type): Token
    {
        $ch = $this->input[$this->pos];
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;
        $this->advance();
        return new Token($type, $ch, $ch, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function makeTokenAndAdvance(TokenType $type, string $value, string $raw, int $length): Token
    {
        $start = $this->pos;
        $startLine = $this->line;
        $startColumn = $this->column;
        $this->advanceBy($length);
        return new Token($type, $value, $raw, new Span($start, $this->pos, $startLine, $startColumn));
    }

    private function makeToken(TokenType $type, string $value, string $raw, int $start): Token
    {
        return new Token($type, $value, $raw, new Span($start, $this->pos, $this->line, $this->column));
    }

    private function advance(): void
    {
        if ($this->pos < $this->length) {
            if ($this->input[$this->pos] === "\n") {
                $this->line++;
                $this->column = 1;
            } else {
                $this->column++;
            }
            $this->pos++;
        }
    }

    private function advanceBy(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->advance();
        }
    }

    private function peek(int $offset): ?string
    {
        $pos = $this->pos + $offset;
        return $pos < $this->length ? $this->input[$pos] : null;
    }

    private function matchAhead(string $str): bool
    {
        $len = strlen($str);
        if ($this->pos + $len > $this->length) {
            return false;
        }
        return substr($this->input, $this->pos, $len) === $str;
    }

    private function pushMode(int $mode): void
    {
        $this->modeStack[] = $this->mode;
        $this->mode = $mode;
    }

    private function popMode(): void
    {
        $this->mode = array_pop($this->modeStack) ?? self::MODE_DEFAULT;
    }

    private function skipWhitespace(): void
    {
        while ($this->pos < $this->length && $this->isWhitespace($this->input[$this->pos])) {
            $this->advance();
        }
    }

    private function isWhitespace(string $ch): bool
    {
        return $ch === ' ' || $ch === "\t" || $ch === "\r" || $ch === "\n";
    }

    private function isNameStartChar(string $ch): bool
    {
        return $ch === ':' || $ch === '_' || ctype_alpha($ch);
    }

    private function isNameChar(string $ch): bool
    {
        return $this->isNameStartChar($ch) || $ch === '-' || $ch === '.' || ctype_digit($ch);
    }
}
