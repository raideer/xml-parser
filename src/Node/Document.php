<?php

declare(strict_types=1);

namespace Raideer\XmlParser\Node;

use Raideer\XmlParser\Node;

class Document extends Node
{
    const TYPE = 'document';

    public $type = self::TYPE;

    /**
     * @return Prolog|null 
     */
    public function getProlog(): ?Prolog
    {
        return $this->getFirstChildNode(Prolog::TYPE);
    }

    /**
     * @return Element|null 
     */
    public function getRootElement(): ?Element
    {
        return $this->getFirstChildNode(Element::TYPE);
    }

    /**
     * @return Misc[] 
     */
    public function getMisc(): array
    {
        return $this->getChildNodesOfType(Misc::TYPE);
    }
}