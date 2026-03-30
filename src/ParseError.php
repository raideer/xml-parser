<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Represents a single parse error with location and optional hint.
 *
 * ParseErrors are collected during parsing and attached to the ParseResult.
 * They can be formatted into human-readable messages with source context.
 */
readonly class ParseError implements \JsonSerializable
{
    /**
     * @param string      $message The error message
     * @param Span        $span    Position in the source where the error occurred
     * @param string|null $hint    Optional hint for how to fix the error
     */
    public function __construct(
        public string $message,
        public Span $span,
        public ?string $hint = null,
    ) {
    }

    /**
     * Formats the error with source context for display.
     *
     * Renders the error message, the relevant source line, and a caret (^)
     * pointing to the error location. Includes the hint if present.
     *
     * @param string $input The full source input for context extraction
     * @return string
     */
    public function format(string $input): string
    {
        $lines = explode("\n", $input);
        $lineIndex = $this->span->line - 1;
        $line = $lines[$lineIndex] ?? '';

        $output = sprintf("Error on line %d, column %d: %s\n", $this->span->line, $this->span->column, $this->message);
        $output .= "  " . $line . "\n";
        $output .= "  " . str_repeat(' ', $this->span->column - 1) . '^';

        if ($this->hint !== null) {
            $output .= "\n  Hint: " . $this->hint;
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'message' => $this->message,
            'span' => $this->span,
            'hint' => $this->hint,
        ];
    }
}
