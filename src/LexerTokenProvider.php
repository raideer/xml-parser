<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

class LexerTokenProvider
{
    private int $position = 0;
    private int $endPosition;

    /**
     * @var Token[]
     */
    private array $tokens;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->endPosition = count($tokens) - 1;
    }

    /**
     * @return int 
     */
    public function currentPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int 
     */
    public function endPosition(): int
    {
        return $this->endPosition;
    }

    /**
     * @param int $pos 
     * @return void 
     */
    public function setCurrentPosition(int $pos): void
    {
        $this->position = $pos;
    }

    /**
     * @return Token 
     */
    public function scanNextToken(): Token
    {
        if ($this->position >= $this->endPosition) {
            return $this->tokens[$this->endPosition];
        }

        return $this->tokens[$this->position++];
    }
}