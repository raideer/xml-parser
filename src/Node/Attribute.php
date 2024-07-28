<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenKind;

class Attribute extends Node
{
    const TYPE = 'attribute';

    public $type = self::TYPE;

    /**
     * Returns the attribute name as string.
     * Returns null if NAME token is not found
     * 
     * @return null|string 
     */
    public function getName(): ?string
    {
        $token = $this->getFirstToken(TokenKind::NAME);

        if (!$token) {
            return null;
        }
        
        return $token->value;
    }

    /**
     * Returns the attribute value as string (without quotes)
     * Returns null if STRING token is not found
     * 
     * @return null|string 
     */
    public function getValue(): ?string
    {
        $token = $this->getFirstToken(TokenKind::STRING);

        if (!$token) {
            return null;
        }
        
        return $token->value;
    }

    /**
     * Returns the attribute value as string (with quotes)
     * Returns null if STRING token is not found
     * 
     * @return null|string 
     */
    public function getFullValue(): ?string
    {
        $token = $this->getFirstToken(TokenKind::STRING);

        if (!$token) {
            return null;
        }
        
        return $token->fullValue;
    }
}