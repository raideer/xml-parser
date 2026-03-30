<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Represents a position range in the source input.
 *
 * Tracks byte offsets (start/end) and human-readable position (line/column).
 */
readonly class Span implements \JsonSerializable
{
    /**
     * @param int $start  Byte offset of the first character (inclusive)
     * @param int $end    Byte offset past the last character (exclusive)
     * @param int $line   1-based line number where the span starts
     * @param int $column 1-based column number where the span starts
     */
    public function __construct(
        public int $start,
        public int $end,
        public int $line,
        public int $column,
    ) {
    }

    /**
     * Returns the byte length of the span.
     *
     * @return int
     */
    public function length(): int
    {
        return $this->end - $this->start;
    }

    /**
     * Merges this span with another, producing a span that covers both ranges.
     *
     * The resulting line/column corresponds to whichever span starts first.
     *
     * @param Span $other The span to merge with
     * @return Span
     */
    public function merge(Span $other): Span
    {
        return new Span(
            min($this->start, $other->start),
            max($this->end, $other->end),
            min($this->line, $other->line) === $this->line ? $this->line : $other->line,
            min($this->start, $other->start) === $this->start ? $this->column : $other->column,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'line' => $this->line,
            'column' => $this->column,
        ];
    }
}
