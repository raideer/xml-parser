<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\TokenKind;

class Element extends Node
{
    const TYPE = 'element';

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
     * @return Attribute[] 
     */
    public function getAttributes(): array
    {
        return $this->getChildNodesOfType(Attribute::TYPE);
    }

    /**
     * @return null|Content 
     */
    public function getContent(): ?Content
    {
        return $this->getFirstChildNode(Content::TYPE);
    }
}