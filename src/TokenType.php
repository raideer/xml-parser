<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

/**
 * Backed string enum representing all possible token types produced by the Lexer.
 */
enum TokenType: string
{
    /** XML comment: <!-- ... --> */
    case Comment = 'Comment';

    /** CDATA section: <![CDATA[ ... ]]> */
    case CData = 'CData';

    /** Document type declaration: <!DOCTYPE ...> */
    case Dtd = 'Dtd';

    /** Entity reference: &name; */
    case EntityRef = 'EntityRef';

    /** Character reference: &#123; or &#xAB; */
    case CharRef = 'CharRef';

    /** Processing instruction: <?name ... ?> */
    case ProcessingInstruction = 'ProcessingInstruction';

    /** Text content between tags */
    case Text = 'Text';

    /** Whitespace outside of tags (sea whitespace) */
    case SeaWhitespace = 'SeaWhitespace';

    /** Opening angle bracket: < */
    case Open = 'Open';

    /** Closing angle bracket: > */
    case Close = 'Close';

    /** Self-closing tag end: /> */
    case SlashClose = 'SlashClose';

    /** Forward slash: / */
    case Slash = 'Slash';

    /** XML declaration open: <?xml */
    case XmlDeclOpen = 'XmlDeclOpen';

    /** Processing instruction / XML declaration close: ?> */
    case SpecialClose = 'SpecialClose';

    /** Equals sign: = */
    case Equals = 'Equals';

    /** Quoted string value: "..." or '...' */
    case String = 'String';

    /** XML name (element name, attribute name, etc.) */
    case Name = 'Name';

    /** Whitespace inside tags */
    case Whitespace = 'Whitespace';

    /** End of file marker */
    case Eof = 'Eof';

    /** Unrecognized or malformed token */
    case Error = 'Error';

    /** Synthetic token inserted when an expected token is absent */
    case Missing = 'Missing';

    /** Unterminated or malformed quoted string */
    case InvalidString = 'InvalidString';

    /** Unexpected < encountered inside a tag */
    case InvalidOpen = 'InvalidOpen';
}
