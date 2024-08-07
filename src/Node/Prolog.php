<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;

class Prolog extends Node
{
    const TYPE = 'prolog';

    public $type = self::TYPE;

    /**
     * Returns all child attribute nodes
     * 
     * @return Attribute[] 
     */
    public function getAttributes(): array
    {
        return $this->getChildNodesOfType(Attribute::TYPE);
    }
}