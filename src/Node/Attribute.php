<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenType;

final class Attribute extends Node
{
    public function getName(): ?string
    {
        $token = $this->getFirstToken(TokenType::Name);

        return $token?->value;
    }

    public function getValue(): ?string
    {
        $token = $this->getFirstToken(TokenType::String, TokenType::InvalidString);

        return $token?->value;
    }

    public function getFullValue(): ?string
    {
        $token = $this->getFirstToken(TokenType::String, TokenType::InvalidString);

        return $token?->rawValue;
    }
}
