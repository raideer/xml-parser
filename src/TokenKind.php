<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

class TokenKind
{
    const COMMENT = 1;
    const CDATA = 2;
    const DTD = 3;
    const ENTITY_REF = 4;
    const CHAR_REF = 5;
    const SEA_WS = 6;
    const OPEN = 7;
    const XML_DECL_OPEN = 8;
    const PI = 9;
    const TEXT = 10;
    const CLOSE = 11;
    const SPECIAL_CLOSE = 12;
    const SLASH_CLOSE = 13;
    const SLASH = 14;
    const EQUALS = 15;
    const STRING = 16;
    const NAME = 17;
    const S = 18;
    const EOF = 21;
    const ERROR = 22;
    const MISSING = 23;

    const KIND_NAME = [
        self::COMMENT => 'COMMENT',
        self::CDATA => 'CDATA',
        self::DTD => 'DTD',
        self::ENTITY_REF => 'ENTITY_REF',
        self::CHAR_REF => 'CHAR_REF',
        self::SEA_WS => 'SEA_WS',
        self::OPEN => 'OPEN',
        self::XML_DECL_OPEN => 'XML_DECL_OPEN',
        self::PI => 'PI',
        self::TEXT => 'TEXT',
        self::CLOSE => 'CLOSE',
        self::SPECIAL_CLOSE => 'SPECIAL_CLOSE',
        self::SLASH_CLOSE => 'SLASH_CLOSE',
        self::SLASH => 'SLASH',
        self::EQUALS => 'EQUALS',
        self::STRING => 'STRING',
        self::NAME => 'NAME',
        self::S => 'S',
        self::EOF => 'EOF',
        self::ERROR => 'ERROR',
        self::MISSING => 'MISSING',
    ];
}
