<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Lexer;

use Raideer\XmlParser\TokenKind;

class Grammar
{
    private const FRAGMENT_HEXDIGIT = '[a-fA-F0-9]';
    private const FRAGMENT_DIGIT = '[0-9]';
    private const FRAGMENT_NAME_START_CHAR = '[:_a-zA-Z]|\x{2070}-\x{218F}|\x{2C00}-\x{2FEF}|\x{3001}-\x{D7FF}|\x{F900}-\x{FDCF}|\x{FDF0}-\x{FFFD}';
    private const FRAGMENT_NAME_CHAR = self::FRAGMENT_NAME_START_CHAR  . '|-|\.|\x{00B7}|\d|\x{0300}-\x036F|\x{203F}-\x{2040}';
    private const FRAGMENT_NAME = '(?:' . self::FRAGMENT_NAME_START_CHAR . ')(?:' . self::FRAGMENT_NAME_CHAR . ')*';
    private const FRAGMENT_S = '[ \t\r\n]';

    /**
     * Declares rules that the lexer will use to produce tokens
     * 
     * @return Rule[] 
     */
    public static function rules()
    {
        return [
            // Default mode: everything outside of a tag
            new Rule(
                TokenKind::COMMENT,
                '<!--((?:.|\r?\n)*?)-->',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::CDATA,
                '<!\[CDATA\[(.*)\]\]>',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::DTD,
                '<!.*?>',
                [Rule::MODE_DEFAULT],
                Rule::FLAG_SKIP
            ),
            new Rule(
                TokenKind::ENTITY_REF,
                '&(' . self::FRAGMENT_NAME . ');',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::CHAR_REF,
                '&#(?:' . self::FRAGMENT_DIGIT . '|x' . self::FRAGMENT_HEXDIGIT . ')+;',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::SEA_WS,
                '[ \t\r\n]+',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::XML_DECL_OPEN,
                '<\?xml(?:' . self::FRAGMENT_S . ')',
                [Rule::MODE_DEFAULT],
                Rule::FLAG_PUSH_INSIDE
            ),
            new Rule(
                TokenKind::PI,
                '<\?(' . self::FRAGMENT_NAME . ').*\?>',
                [Rule::MODE_DEFAULT],
            ),
            new Rule(
                TokenKind::OPEN,
                '<',
                [Rule::MODE_DEFAULT],
                Rule::FLAG_PUSH_INSIDE
            ),
            new Rule(
                TokenKind::TEXT,
                '[^<]+',
                [Rule::MODE_DEFAULT],
            ),

            // Inside mode: everything inside a tag
            new Rule(
                TokenKind::CLOSE,
                '>',
                [Rule::MODE_INSIDE],
                Rule::FLAG_POP_MODE
            ),
            new Rule(
                TokenKind::SPECIAL_CLOSE,
                '\?>',
                [Rule::MODE_INSIDE],
                Rule::FLAG_POP_MODE
            ),
            new Rule(
                TokenKind::SLASH_CLOSE,
                '\/>',
                [Rule::MODE_INSIDE],
                Rule::FLAG_POP_MODE
            ),
            new Rule(
                TokenKind::SLASH,
                '\/',
                [Rule::MODE_INSIDE],
            ),
            new Rule(
                TokenKind::EQUALS,
                '=',
                [Rule::MODE_INSIDE],
            ),
            new Rule(
                TokenKind::STRING,
                '"[^<"]*"|\'[^<\']*\'',
                [Rule::MODE_INSIDE],
            ),
            new Rule(
                TokenKind::NAME,
                self::FRAGMENT_NAME,
                [Rule::MODE_INSIDE],
            ),
            new Rule(
                TokenKind::S,
                self::FRAGMENT_S,
                [Rule::MODE_INSIDE],
                Rule::FLAG_SKIP
            ),
        ];
    }
}
