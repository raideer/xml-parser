<?php

namespace Raideer\XmlParser;

use JsonSerializable;

class Token implements JsonSerializable
{
    public int $kind;
    public string $value;
    public string $fullValue;
    public int $offset;
    public int $fullOffset;

    public function __construct(
        int $kind,
        string $fullValue,
        string $value,
        int $offset,
        int $fullOffset,
    ) {
        $this->kind = $kind;
        $this->fullValue = $fullValue;
        $this->value = $value;
        $this->offset = $offset;
        $this->fullOffset = $fullOffset;
    }

    /**
     * JSON serialize token for debugging purposes
     * 
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'kind' => TokenKind::KIND_NAME[$this->kind],
            'value' => $this->value,
            'fullValue' => $this->fullValue,
            'offset' => $this->offset,
            'fullOffset' => $this->fullOffset,
        ];
    }
}
