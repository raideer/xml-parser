<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenKind;

class Reference extends Node
{
    const TYPE = 'reference';

    public $type = self::TYPE;

    /**
     * Returns the entity reference as string.
     * 
     * @return null|string 
     */
    public function getEntityRef(): ?string
    {
        $token = $this->getFirstToken(TokenKind::ENTITY_REF);

        if (!$token) {
            return null;
        }
        
        return $token->value;
    }

    /**
     * Returns the character reference as string.
     * 
     * @return null|string 
     */
    public function getCharRef(): ?string
    {
        $token = $this->getFirstToken(TokenKind::CHAR_REF);

        if (!$token) {
            return null;
        }
        
        return $token->value;
    }
}