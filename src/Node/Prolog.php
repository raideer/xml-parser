<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;

final class Prolog extends Node
{
    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->getChildrenOfType(Attribute::class);
    }
}
