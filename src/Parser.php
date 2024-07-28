<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

class Parser
{
    private Lexer $lexer;
    private Token $token;
    
    /**
     * @var LexerTokenProvider
     */
    private $tokenProvider;

    public function __construct()
    {
        $this->lexer = new Lexer();
    }

    /**
     * Parses an XML string into a Document node
     *
     * @return Node\Document
     */
    public function parse(string $xml): Node\Document
    {
        $this->tokenProvider = new LexerTokenProvider(
            $this->lexer->tokenize($xml)
        );

        $this->token = $this->tokenProvider->scanNextToken();

        return  $this->parseDocument();
    }

    /**
     * Represents the whole XML document
     *
     * @return Node\Document
     */
    private function parseDocument()
    {
        $document = new Node\Document();
        
        $document->addChildren(
            $this->parseProlog(),
            $this->parseMisc(),
            $this->parseElement(),
            $this->parseMisc(),
        );

        $document->addToken(
            $this->consumeOptional(TokenKind::EOF)
        );

        return $document;
    }

    /**
     * Parses the prolog part of the document
     *
     * Example: <?xml version="1.0"?>
     *
     * @return null|Node\Prolog
     */
    private function parseProlog()
    {
        if ($this->token->kind !== TokenKind::XML_DECL_OPEN) {
            return null;
        }

        $prolog = new Node\Prolog();

        $prolog->addToken(
            $this->consume(TokenKind::XML_DECL_OPEN)
        );

        while ($attribute = $this->parseAttribute()) {
            $prolog->addChild($attribute);
        }

        $prolog->addToken(
            $this->consume(TokenKind::SPECIAL_CLOSE)
        );

        return $prolog;
    }

    /**
     * Misc consists of any comments, processing instructions or whitespace
     * that are not part of any element
     *
     * @return null|Node\Misc
     */
    private function parseMisc()
    {
        $miscTokens = $this->consumeAllOptional(TokenKind::COMMENT, TokenKind::PI, TokenKind::SEA_WS);

        if (!$miscTokens) {
            return null;
        }

        $misc = new Node\Misc();
        $misc->addTokens(...$miscTokens);
        return $misc;
    }

    /**
     * Element can be a self closing element tag <element />
     * or a tag with Content <element>content</element>
     * @return null|Node\Element
     */
    private function parseElement()
    {
        if ($this->token->kind !== TokenKind::OPEN || $this->lookahead(TokenKind::SLASH, TokenKind::NAME)) {
            return null;
        }

        $element = new Node\Element();

        $element->addTokens(
            $this->consume(TokenKind::OPEN),
            $this->consume(TokenKind::NAME),
        );

        while ($attribute = $this->parseAttribute()) {
            $element->addChild($attribute);
        }

        // <element>...</element>
        if ($this->token->kind === TokenKind::CLOSE) {
            $element->addToken(
                $this->consume(TokenKind::CLOSE)
            );

            $element->addChild(
                $this->parseContent(),
            );

            $element->addTokens(
                $this->consume(TokenKind::OPEN),
                $this->consume(TokenKind::SLASH),
                $this->consume(TokenKind::NAME),
                $this->consume(TokenKind::CLOSE)
            );
            // <element />
        } else {
            $element->addToken(
                $this->consume(TokenKind::SLASH_CLOSE)
            );
        }

        return $element;
    }

    /**
     * Content of an element.
     *
     * CharData? ((Element | Reference | CData | PI | Comment) CharData?)*
     *
     * @return null|Node\Content
     */
    private function parseContent()
    {
        $content = new Node\Content();

        $content->addChild(
            $this->parseCharData(),
        );

        while ($this->parseContentInner($content)) {
            // Keep parsing
        }

        $content->addChild(
            $this->parseCharData(),
        );

        if (count($content->children) === 0) {
            return null;
        }

        return $content;
    }

    /**
     * (Element | Reference | CData | PI | Comment) CharData?
     *
     * @var Node\Content $content
     * @return bool
     */
    private function parseContentInner(Node\Content $content)
    {
        if ($this->parseContentMidSection($content)) {
            $content->addChild(
                $this->parseCharData()
            );

            return true;
        }

        return false;
    }

    /**
     * Element | Reference | CData | PI | Comment
     *
     * @var Node\Content $content
     * @return bool
     */
    private function parseContentMidSection(Node\Content $content)
    {
        if ($element = $this->parseElement()) {
            $content->addChild($element);
            return true;
        }

        if ($reference = $this->parseReference()) {
            $content->addChild($reference);
            return true;
        }

        if ($token = $this->consumeOptional(TokenKind::CDATA, TokenKind::PI, TokenKind::COMMENT)) {
            $content->addToken($token);
            return true;
        }

        return false;
    }

    /**
     * Reference is either an entity reference or a character reference
     *
     * Example:
     * - &#x3C; (CharRef)
     * - &docdate; (EntityRef)
     *
     * @return null|Node\Reference
     */
    private function parseReference()
    {
        $refTokens = $this->consumeAllOptional(TokenKind::ENTITY_REF, TokenKind::CHAR_REF);

        if (!$refTokens) {
            return null;
        }

        $misc = new Node\Reference();
        $misc->addTokens(...$refTokens);
        return $misc;
    }

    /**
     * Character data consists of either plain text or whitespace
     *
     * @return null|Node\CharData
     */
    private function parseCharData()
    {
        $charDataTokens = $this->consumeAllOptional(TokenKind::TEXT, TokenKind::SEA_WS);

        if (!$charDataTokens) {
            return null;
        }

        $misc = new Node\CharData();
        $misc->addTokens(...$charDataTokens);
        return $misc;
    }

    /**
     * @return null|Node\Attribute
     */
    private function parseAttribute()
    {
        if ($this->token->kind !== TokenKind::NAME) {
            return null;
        }

        $attribute = new Node\Attribute();

        $attribute->addTokens(
            $this->consume(TokenKind::NAME),
            $this->consume(TokenKind::EQUALS),
            $this->consume(TokenKind::STRING)
        );

        return $attribute;
    }

    /**
     * @param int[] $kinds
     * @return bool
     */
    private function lookahead(int ...$kinds): bool
    {
        $startPos = $this->tokenProvider->currentPosition();
        $startToken = $this->token;
        $succeeded = true;

        foreach ($kinds as $kind) {
            $token = $this->tokenProvider->scanNextToken();
            $currentPosition = $this->tokenProvider->currentPosition();
            $endPosition = $this->tokenProvider->endPosition();

            if ($currentPosition > $endPosition || $token->kind !== $kind) {
                $succeeded = false;
                break;
            }
        }
        
        $this->tokenProvider->setCurrentPosition($startPos);
        $this->token = $startToken;
        return $succeeded;
    }
    
    /**
     * @param int $kind
     * @return Token
     */
    private function consume(int $kind): Token
    {
        $token = $this->token;

        if ($token->kind === $kind) {
            $this->token = $this->tokenProvider->scanNextToken();
            return $token;
        }

        return new Token(TokenKind::MISSING, '', '', $token->offset, $token->offset);
    }

    /**
     * @param int[] $kinds
     * @return null|Token
     */
    private function consumeOptional(int ...$kinds): ?Token
    {
        $token = $this->token;

        if (in_array($token->kind, $kinds)) {
            $this->token = $this->tokenProvider->scanNextToken();
            return $token;
        }

        return null;
    }

    /**
     * @param int[] $kinds
     * @return null|Token[]
     */
    private function consumeAllOptional(int ...$kinds): ?array
    {
        $tokens = [];

        while ($token = $this->consumeOptional(...$kinds)) {
            $tokens[] = $token;
        }

        if (count($tokens) === 0) {
            return null;
        }

        return $tokens;
    }
}
