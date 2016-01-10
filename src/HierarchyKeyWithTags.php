<?php

namespace Cache\Hierarchy;

final class HierarchyKeyWithTags implements HierarchyKeyInterface
{
    /**
     * @var \Cache\Hierarchy\HierarchyKey
     */
    private $hierarchyKey;
    private $tagSeparator;

    public function __construct(HierarchyKeyInterface $hierarchyKey, $tagSeparator)
    {
        $this->hierarchyKey = $hierarchyKey;
        $this->tagSeparator = $tagSeparator;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->hierarchyKey->isValid();
    }

    /**
     * @return array
     */
    public function iterator()
    {
        list(, $tag) = explode($this->tagSeparator, $this->hierarchyKey.$this->tagSeparator);

        return array_map(function($hierarchicalKeyPart) use ($tag) {
            return $hierarchicalKeyPart.':'.$tag;
        }, $this->hierarchyKey->iterator());
    }

    public function __toString()
    {
        return (string) $this->hierarchyKey;
    }
}
