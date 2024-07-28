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
     * @return Element[] 
     */
    public function getElements(): array
    {
        return $this->getChildNodesOfType(Element::TYPE);
    }

    /**
     * @return Reference[] 
     */
    public function getReferences(): array
    {
        return $this->getChildNodesOfType(Reference::TYPE);
    }

    /**
     * @return CharData[] 
     */
    public function getCharData(): array
    {
        return $this->getChildNodesOfType(CharData::TYPE);
    }

    /**
     * @return string[] 
     */
    public function getCData(): array
    {
        return array_map(function (Token $token) {
            return $token->value;
        }, $this->getChildTokensOfType(TokenKind::CDATA));
    }

    /**
     * @return string[] 
     */
    public function getComments(): array
    {
        return array_map(function (Token $token) {
            return $token->value;
        }, $this->getChildTokensOfType(TokenKind::COMMENT));
    }
}