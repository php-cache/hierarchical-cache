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
        $hierarchyKeyParts = $this->hierarchyKey->iterator();
        $lastHierarchyKeyPart = array_pop($hierarchyKeyParts);

        list ($lastHierarchyKeyPart, $tag) = explode(
          $this->tagSeparator,
          $lastHierarchyKeyPart . $this->tagSeparator
        );

        return array_map(
          function($hierarchicalKeyPart) use ($tag) {
              return "{$hierarchicalKeyPart}:{$tag}";
          },
          array_merge(
            $hierarchyKeyParts,
            array_filter($lastHierarchyKeyPart)
          )
        );
    }
}
