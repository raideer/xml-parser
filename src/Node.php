<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

abstract class Node implements NodeInterface
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
     * @var NodeInterface[]
     */
    public $children = [];

    /**
     * Adds a child node
     *
     * @param null|NodeInterface $child
     * @return void
     */
    public function addChild(?NodeInterface $child)
    {
        if (!$child) {
            return;
        }

        $child->parent = $this;
        $this->children[] = $child;
    }

    /**
     * Adds multiple child nodes
     * @param NodeInterface|null $children
     * @return void
     */
    public function addChildren(...$children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * Returns the parent node
     *
     * @return Node|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * Returns the first child node of a given type
     *
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
     * Returns the first token of a given kind
     * See: TokenKind class
     *
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
     * Returns all child nodes of a given type
     *
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
     * Returns all child tokens of a given kind
     * See: TokenKind class
     *
     * @param int $kinds
     * @return Token[]
     */
    public function getChildTokensOfType(...$kinds)
    {
        return array_filter($this->children, function ($child) use ($kinds) {
            return in_array($child->kind, $kinds) && $child instanceof Token;
        });
    }

    /**
     * Walks through all descendant nodes and tokens
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
     * Walks through all descendant nodes
     *
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
     * Walks through all descendant tokens
     *
     * @param callable $callback
     * @return mixed
     */
    public function walkDescendantTokens(callable $callback)
    {
        foreach ($this->children as $child) {
            if ($child instanceof Token) {
                $callback($child);
            } elseif ($child instanceof Node) {
                $child->walkDescendantTokens($callback);
            }
        }

        return null;
    }

    /**
     * Returns the root node. Will return self if node has no parent
     *
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
     * Returns all child nodes and tokens
     *
     * @return (Node|Token)[]
     */
    public function getChildNodesAndTokens()
    {
        return $this->children;
    }

    /**
     * Returns all child nodes
     *
     * @return Node[]
     */
    public function getChildNodes()
    {
        return array_filter($this->children, function ($child) {
            return $child instanceof Node;
        });
    }

    /**
     * Returns all child tokens
     *
     * @return Token[]
     */
    public function getChildTokens()
    {
        return array_filter($this->children, function ($child) {
            return $child instanceof Token;
        });
    }

    /**
     * @param int $offset
     * @return Token|null
     */
    public function getTokenAtOffset(int $offset)
    {
        foreach ($this->children as $child) {
            if ($child instanceof Token) {
                $end = $child->fullOffset + strlen($child->fullValue);

                if ($child->fullOffset <= $offset && $end >= $offset) {
                    return $child;
                }
            }
        }

        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $token = $child->getTokenAtOffset($offset);

                if ($token) {
                    return $token;
                }
            }
        }

        return null;
    }

    /**
     * JSON serialize node for debugging purposes
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'children' => $this->getChildNodes(),
            'tokens' => $this->getChildTokens()
        ];
    }

}
