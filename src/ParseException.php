<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Exception thrown when parsing XML with errors via the strict Xml::parse() method.
 *
 * Contains the full list of ParseError objects. The exception message is a
 * formatted, human-readable concatenation of all errors with source context.
 */
class ParseException extends \RuntimeException
{
    /** @var ParseError[] */
    public readonly array $errors;

    /**
     * @param ParseError[] $errors The parse errors that caused this exception
     * @param string       $input  The original XML input (used for error formatting)
     */
    public function __construct(array $errors, string $input)
    {
        $this->errors = $errors;

        $messages = array_map(
            fn (ParseError $error) => $error->format($input),
            $errors,
        );

        parent::__construct(implode("\n\n", $messages));
    }
}
