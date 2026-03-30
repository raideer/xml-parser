<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenType;

final class Reference extends Node
{
    public function getEntityRef(): ?string
    {
        $token = $this->getFirstToken(TokenType::EntityRef);

        return $token?->value;
    }

    public function getCharRef(): ?string
    {
        $token = $this->getFirstToken(TokenType::CharRef);

        return $token?->value;
    }
}
