<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenKind;

class CharData extends Node
{
    const TYPE = 'charData';

    public $type = self::TYPE;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        $token = $this->getFirstToken(TokenKind::TEXT);

        if (!$token) {
            return null;
        }
        
        return $token->value;
    }
}