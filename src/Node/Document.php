<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;

final class Document extends Node
{
    public function getProlog(): ?Prolog
    {
        return $this->getFirstChildOfType(Prolog::class);
    }

    public function getRootElement(): ?Element
    {
        return $this->getFirstChildOfType(Element::class);
    }

    /**
     * @return Misc[]
     */
    public function getMisc(): array
    {
        return $this->getChildrenOfType(Misc::class);
    }
}
