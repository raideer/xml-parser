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
}