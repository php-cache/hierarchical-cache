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
     * @throws InvalidArgumentException
     */
    protected function getHierarchyKey($key, &$pathKey = null)
    {
        if (!$this->isHierarchyKey($key)) {
            return $key;
        }

        $key = $this->explodeKey($key);

        $keyString = '';
        $pathKey = 'path:';
        foreach ($key as $name) {
            $keyString .= $name;
            $pathKey = 'path:' . $keyString;

            if (isset($this->keyCache[$pathKey])) {
                $index = $this->keyCache[$pathKey];
            } else {
                $index = $this->getValueForStore($pathKey);
                $this->keyCache[$pathKey] = $index;
            }

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


    /**
     * A hierarchy key MUST begin with the separator.
     *
     * @param string $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    private function isHierarchyKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf('Key must be string.'));
        }

        return substr($key, 0, 1) === HierarchicalPoolInterface::HIERARCHY_SEPARATOR;
    }


    /**
     * @param string $key
     *
     * @return array
     */
    private function explodeKey($string)
    {
        list($key, $tag) = explode(TaggablePoolInterface::TAG_SEPARATOR, $string.TaggablePoolInterface::TAG_SEPARATOR.TaggablePoolInterface::TAG_SEPARATOR);
        $parts = explode(HierarchicalPoolInterface::HIERARCHY_SEPARATOR, $key);
        unset($parts[0]);

        return array_map(function ($a) use ($tag) {return $a.':'.$tag;},$parts);
    }
}