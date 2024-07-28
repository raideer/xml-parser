<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\Token;
use Raideer\XmlParser\TokenKind;

class Content extends Node
{
    const TYPE = 'content';

    public $type = self::TYPE;

    /**
     * Returns all child element nodes
     * 
     * @return Element[] 
     */
    public function getElements(): array
    {
        return $this->getChildNodesOfType(Element::TYPE);
    }

    /**
     * Returns all child reference nodes
     * 
     * @return Reference[] 
     */
    public function getReferences(): array
    {
        return $this->getChildNodesOfType(Reference::TYPE);
    }

    /**
     * Returns all child charData nodes
     * 
     * @return CharData[] 
     */
    public function getCharData(): array
    {
        return $this->getChildNodesOfType(CharData::TYPE);
    }

    /**
     * Returns all CData strings
     * 
     * @return string[] 
     */
    public function getCData(): array
    {
        return array_map(function (Token $token) {
            return $token->value;
        }, $this->getChildTokensOfType(TokenKind::CDATA));
    }

    /**
     * Returns all comment strings
     * 
     * @return string[] 
     */
    public function getComments(): array
    {
        return array_map(function (Token $token) {
            return $token->value;
        }, $this->getChildTokensOfType(TokenKind::COMMENT));
    }
}