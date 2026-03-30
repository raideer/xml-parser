<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Represents a single lexical token produced by the Lexer.
 *
 * Tokens are leaf nodes in the AST. Each token carries its type, parsed value,
 * raw source text, and position information via a Span.
 */
readonly class Token implements \JsonSerializable
{
    /**
     * @param TokenType $type     The token type
     * @param string    $value    The parsed/inner value (e.g. without quotes for strings)
     * @param string    $rawValue The full matched source text
     * @param Span      $span     Position information in the source input
     */
    public function __construct(
        public TokenType $type,
        public string $value,
        public string $rawValue,
        public Span $span,
    ) {
    }

    /**
     * Checks whether this token matches any of the given types.
     *
     * @param TokenType ...$types One or more token types to check against
     * @return bool
     */
    public function is(TokenType ...$types): bool
    {
        foreach ($types as $type) {
            if ($this->type === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type->value,
            'value' => $this->value,
            'rawValue' => $this->rawValue,
            'span' => $this->span,
        ];
    }
}
