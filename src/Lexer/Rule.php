<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Lexer;

class Rule
{
    const FLAG_NONE = 0x0;

    /**
     * The resulting token will be skipped from the output
     */
    const FLAG_SKIP = 0x1;

    /**
     * Lexer will "pop" the last mode in the stack
     */
    const FLAG_POP_MODE = 0x2;
    
    /**
     * Pushes "inside" mode to the stack
     */
    const FLAG_PUSH_INSIDE = 0x4;

    /**
     * For parsing everything outside of an element 
     */
    const MODE_DEFAULT = 1;

    /**
     * For parsing everything inside of an element
     */
    const MODE_INSIDE = 2;

    public int $token;
    public string $pattern;
    public array $modes;
    public int $flags = self::FLAG_NONE;

    public function __construct(int $token, string $pattern, array $modes, int $flags = self::FLAG_NONE)
    {
        $this->token = $token;
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->modes = $modes;
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