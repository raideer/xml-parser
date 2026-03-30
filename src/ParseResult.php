<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Non-throwing result container for XML parsing.
 *
 * Always contains a Document (which may be partial on error) and any
 * errors encountered during parsing. Use hasErrors() to check success.
 */
readonly class ParseResult
{
    /**
     * @param Node\Document $document The parsed document (may be partial if errors occurred)
     * @param ParseError[]  $errors   Parse errors encountered during parsing
     */
    public function __construct(
        public Node\Document $document,
        public array $errors = [],
    ) {
    }

    /**
     * Returns whether any parse errors were encountered.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
