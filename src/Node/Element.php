<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenType;

final class Element extends Node
{
    public function getName(): ?string
    {
        $token = $this->getFirstToken(TokenType::Name);

        return $token?->value;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->getChildrenOfType(Attribute::class);
    }

    public function getContent(): ?Content
    {
        return $this->getFirstChildOfType(Content::class);
    }
}
