<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

abstract class Node implements \JsonSerializable
{
    protected ?Node $parent = null;

    /** @var array<Node|Token> */
    protected array $children = [];

    public function addChild(Node|Token|null $child): void
    {
        if ($child === null) {
            return;
        }

        if ($child instanceof Node) {
            $child->parent = $this;
        }

        $this->children[] = $child;
    }

    public function addChildren(Node|Token|null ...$children): void
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @return array<Node|Token>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getSpan(): ?Span
    {
        $first = null;
        $last = null;

        foreach ($this->children as $child) {
            $span = $child instanceof Token ? $child->span : $child->getSpan();
            if ($span === null) {
                continue;
            }
            if ($first === null) {
                $first = $span;
            }
            $last = $span;
        }

        if ($first === null) {
            return null;
        }

        return $first->merge($last);
    }

    /**
     * @template T of Node
     * @param class-string<T> $class
     * @return T|null
     */
    public function getFirstChildOfType(string $class): ?Node
    {
        foreach ($this->children as $child) {
            if ($child instanceof $class) {
                return $child;
            }
        }

        return null;
    }

    public function getFirstToken(TokenType ...$types): ?Token
    {
        foreach ($this->children as $child) {
            if ($child instanceof Token && $child->is(...$types)) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @template T of Node
     * @param class-string<T> $class
     * @return T[]
     */
    public function getChildrenOfType(string $class): array
    {
        return array_values(array_filter(
            $this->children,
            fn (Node|Token $child) => $child instanceof $class,
        ));
    }

    /**
     * @return Token[]
     */
    public function getChildTokensOfType(TokenType ...$types): array
    {
        return array_values(array_filter(
            $this->children,
            fn (Node|Token $child) => $child instanceof Token && $child->is(...$types),
        ));
    }

    /**
     * @return Node[]
     */
    public function getChildNodes(): array
    {
        return array_values(array_filter(
            $this->children,
            fn (Node|Token $child) => $child instanceof Node,
        ));
    }

    /**
     * @return Token[]
     */
    public function getChildTokens(): array
    {
        return array_values(array_filter(
            $this->children,
            fn (Node|Token $child) => $child instanceof Token,
        ));
    }

    public function walkDescendantNodes(callable $callback): void
    {
        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $callback($child);
                $child->walkDescendantNodes($callback);
            }
        }
    }

    public function walkDescendantTokens(callable $callback): void
    {
        foreach ($this->children as $child) {
            if ($child instanceof Token) {
                $callback($child);
            } elseif ($child instanceof Node) {
                $child->walkDescendantTokens($callback);
            }
        }
    }

    public function walkDescendantNodesAndTokens(callable $callback): void
    {
        foreach ($this->children as $child) {
            $callback($child);

            if ($child instanceof Node) {
                $child->walkDescendantNodesAndTokens($callback);
            }
        }
    }

    public function getRoot(): Node
    {
        $node = $this;

        while ($node->parent !== null) {
            $node = $node->parent;
        }

        return $node;
    }

    public function getTokenAtOffset(int $offset): ?Token
    {
        foreach ($this->children as $child) {
            if ($child instanceof Token) {
                if ($child->span->start <= $offset && $child->span->end >= $offset) {
                    return $child;
                }
            }
        }

        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $token = $child->getTokenAtOffset($offset);
                if ($token !== null) {
                    return $token;
                }
            }
        }

        return null;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => (new \ReflectionClass($this))->getShortName(),
            'children' => $this->children,
        ];
    }
}
