<?php

namespace Cache\Hierarchy;

use Cache\Adapter\Common\Exception\InvalidArgumentException;
use Cache\Taggable\TaggablePoolInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait HierarchicalCachePoolTrait
{
    /**
     * A temporary cache for keys
     *
     * @type array
     */
    private $keyCache = [];

    /**
     * Get a value form the store. This must not be an PoolItemInterface.
     *
     * @param string $key
     *
     * @return string|null
     */
    abstract protected function getValueForStore($key);


    /**
     * Get a key to use with the hierarchy. If the key does not start with HierarchicalPoolInterface::SEPARATOR
     * this will return an unalterered key. This function supports a tagged key. Ie "foo:bar".
     *
     * @param string $key The original key
     * @param string &$pathKey A cache key for the path. If this key is changed everything beyond that path is changed.
     *
     * @return string
     */
    protected function getHierarchyKey($key, &$pathKey = null)
    {
        $hierarchyKey = new HierarchyKeyWithTags(
          new HierarchyKey($key),
          TaggablePoolInterface::TAG_SEPARATOR
        );

        if (!$hierarchyKey->isValid()) {
            return $key;
        }

        $keyString = '';
        $pathKey = 'path:';

        foreach ($hierarchyKey->iterator() as $name) {
            $keyString .= $name;
            $pathKey = 'path:' . $keyString;

            if (!array_key_exists($pathKey, $this->keyCache)) {
                $this->keyCache[$pathKey] = $this->getValueForStore($pathKey);
            }

            $index = $this->keyCache[$pathKey];
            $keyString .= ':' . $index . ':';
        }

        return $keyString;
    }

    /**
     * Clear the cache for the keys
     */
    protected function clearHierarchyKeyCache()
    {
        $this->keyCache = [];
    }
}
