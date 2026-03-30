<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenType;

final class CharData extends Node
{
    public function getText(): ?string
    {
        $token = $this->getFirstToken(TokenType::Text);

        return $token?->value;
    }
}
