<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Facade for parsing XML strings.
 *
 * Provides two entry points: parse() for strict parsing (throws on errors)
 * and tryParse() for error-tolerant parsing (returns a result container).
 */
final class Xml
{
    /**
     * Parses an XML string and returns the Document node.
     *
     * Throws a ParseException if any parse errors are encountered.
     * Use tryParse() instead when error tolerance is needed.
     *
     * @param string $xml The XML string to parse
     * @return Node\Document
     * @throws ParseException If the input contains parse errors
     */
    public static function parse(string $xml): Node\Document
    {
        $result = self::tryParse($xml);

        if ($result->hasErrors()) {
            throw new ParseException($result->errors, $xml);
        }

        return $result->document;
    }

    /**
     * Parses an XML string and returns a ParseResult (non-throwing).
     *
     * The result always contains a Document (which may be partial) and any
     * errors encountered. Suitable for IDE tools where error tolerance is needed.
     *
     * @param string $xml The XML string to parse
     * @return ParseResult
     */
    public static function tryParse(string $xml): ParseResult
    {
        $parser = new Parser();
        return $parser->parse($xml);
    }
}
