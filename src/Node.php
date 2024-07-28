<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

use JsonSerializable;

abstract class Node implements JsonSerializable
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var Node|null
     */
    public $parent;

    /**
     * @var (Node|Token)[]
     */
    public $children = [];

    /**
     * @param null|Token $token
     * @return void
     */
    public function addToken(?Token $token)
    {
        if (!$token) {
            return;
        }

        $this->children[] = $token;
    }

    /**
     * @param null|Token $tokens
     * @return void
     */
    public function addTokens(...$tokens)
    {
        foreach ($tokens as $token) {
            $this->addToken($token);
        }
    }

    /**
     * @param null|Node $child
     * @return void
     */
    public function addChild(?Node $child)
    {
        if (!$child) {
            return;
        }

        $child->parent = $this;
        $this->children[] = $child;
    }

    /**
     * @param Node|null $children
     * @return void
     */
    public function addChildren(...$children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @return Node|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @param string $types
     * @return Node|null
     */
    public function getFirstChildNode(...$types)
    {
        foreach ($this->children as $child) {
            if (in_array($child->type, $types) && $child instanceof Node) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param int $kinds 
     * @return Token|null 
     */
    public function getFirstToken(...$kinds)
    {
        foreach ($this->children as $child) {
            if (in_array($child->kind, $kinds) && $child instanceof Token) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param string $types 
     * @return Node[] 
     */
    public function getChildNodesOfType(...$types)
    {
        return array_filter($this->children, function ($child) use ($types) {
            return in_array($child->type, $types) && $child instanceof Node;
        });
    }

    /**
     * @param int $kinds 
     * @return (Node|Token)[] 
     */
    public function getChildTokensOfType(...$kinds)
    {
        return array_filter($this->children, function ($child) use ($kinds) {
            return in_array($child->kind, $kinds) && $child instanceof Token;
        });
    }

    /**
     * 
     * @param callable $callback 
     * @return void 
     */
    public function walkDescendantNodesAndTokens(callable $callback)
    {
        foreach ($this->children as $child) {
            $callback($child);

            if ($child instanceof Node) {
                $child->walkDescendantNodesAndTokens($callback);
            }
        }
    }

    /**
     * @param callable $callback 
     * @return void 
     */
    public function walkDescendantNodes(callable $callback)
    {
        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $callback($child);
                $child->walkDescendantNodes($callback);
            }
        }
    }

    /**
     * @return Node 
     */
    public function getRoot(): Node
    {
        $node = $this;

        while ($node->parent !== null) {
            $node = $node->parent;
        }

        return $node;
    }

    /**
     * @return (Node|Token)[]
     */
    public function getChildNodesAndTokens()
    {
        return $this->children;
    }

    /**
     * @return Node[] 
     */
    public function getChildNodes()
    {
        return array_filter($this->children, function ($child) {
            return $child instanceof Node;
        });
    }

    /**
     * @return Token[] 
     */
    public function getChildTokens()
    {
        return array_filter($this->children, function ($child) {
            return $child instanceof Token;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'children' => $this->getChildNodes(),
            'tokens' => $this->getChildTokens()
        ];
    }
}
