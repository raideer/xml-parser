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
     * Returns the element name as string.
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
     * Returns all element attribute nodes
     * 
     * @return Attribute[] 
     */
    public function getAttributes(): array
    {
        return $this->getChildNodesOfType(Attribute::TYPE);
    }

    /**
     * Returns the content node
     * 
     * @return null|Content 
     */
    public function getContent(): ?Content
    {
        return $this->getFirstChildNode(Content::TYPE);
    }
}