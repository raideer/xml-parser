<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Lexer;

class Rule
{
    public const FLAG_NONE = 0x0;

    /**
     * The resulting token will be skipped from the output
     */
    public const FLAG_SKIP = 0x1;

    /**
     * Lexer will "pop" the last mode in the stack
     */
    public const FLAG_POP_MODE = 0x2;

    /**
     * Pushes "inside" mode to the stack
     */
    public const FLAG_PUSH_INSIDE = 0x4;

    /**
     * For parsing everything outside of an element
     */
    public const MODE_DEFAULT = 1;

    /**
     * For parsing everything inside of an element
     */
    public const MODE_INSIDE = 2;

    public int $token;
    public string $pattern;
    public array $modes;
    public int $flags = self::FLAG_NONE;
    public ?int $category;

    public function __construct(
        int $token,
        string $pattern,
        array $modes,
        int $flags = self::FLAG_NONE,
        ?int $category = null
    ) {
        $this->token = $token;
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->modes = $modes;
        $this->category = $category;

    }

    /**
     * @param int $flag
     * @return bool
     */
    public function hasFlag(int $flag): bool
    {
        return !!($this->flags & $flag);
    }
}
