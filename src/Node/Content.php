<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;
use Raideer\XmlParser\Token;
use Raideer\XmlParser\TokenType;

final class Content extends Node
{
    /**
     * @return Element[]
     */
    public function getElements(): array
    {
        return $this->getChildrenOfType(Element::class);
    }

    /**
     * @return Reference[]
     */
    public function getReferences(): array
    {
        return $this->getChildrenOfType(Reference::class);
    }

    /**
     * @return CharData[]
     */
    public function getCharData(): array
    {
        return $this->getChildrenOfType(CharData::class);
    }

    /**
     * @return string[]
     */
    public function getCData(): array
    {
        return array_map(
            fn (Token $token) => $token->value,
            $this->getChildTokensOfType(TokenType::CData),
        );
    }

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return array_map(
            fn (Token $token) => $token->value,
            $this->getChildTokensOfType(TokenType::Comment),
        );
    }
}
