<?php

namespace Raideer\XmlParser;

use JsonSerializable;

class Token implements JsonSerializable
{
    public int $kind;
    public string $value;
    public string $fullValue;
    public int $offset;

    public function __construct(int $kind, string $fullValue, string $value, int $offset)
    {
        $this->kind = $kind;
        $this->fullValue = $fullValue;
        $this->value = $value;
        $this->offset = $offset;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'kind' => TokenKind::KIND_NAME[$this->kind],
            'value' => $this->value,
            'offset' => $this->offset,
        ];
    }
}