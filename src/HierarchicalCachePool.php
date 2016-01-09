<?php

/*
 * This file is part of php-cache\hierarchical-cache package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Hierarchy;

use Cache\Adapter\Common\CacheItem;
use Cache\Adapter\Common\Exception\InvalidArgumentException;
use Cache\Taggable\TaggableItemInterface;
use Cache\Taggable\TaggablePoolInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HierarchicalCachePool implements CacheItemPoolInterface, HierarchicalPoolInterface, TaggablePoolInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     *
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key, array $tags = [])
    {
        $item = $this->cache->getItem($key, $tags);
        if (!$this->isHierarchyKey($key) || !$item->isHit()) {
            return $item;
        }

        if (!$this->validateParents($key, $tags)) {
            return $item;
        }

        // Invalid item
        if ($item instanceof TaggableItemInterface) {
            $key = $item->getTaggedKey();
        } else {
            $key = $item->getKey();
        }

        return new CacheItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [], array $tags = [])
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key, $tags);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key, array $tags = [])
    {
        $hasItem = $this->cache->hasItem($key, $tags);
        if (!$this->isHierarchyKey($key) || $hasItem === false) {
            return $hasItem;
        }

        return $this->validateParents($key, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(array $tags = [])
    {
        return $this->cache->clear($tags);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key, array $tags = [])
    {
        return $this->cache->deleteItem($key, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys, array $tags = [])
    {
        return $this->cache->deleteItems($keys, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $parts = $this->explodeKey($item->getKey());
        $parentKey = '';
        foreach ($parts as $part) {
            $parentKey .= $part;
            $parent = $this->cache->getItem($parentKey);
            $parent->set(null);
            $this->cache->save($parent);
        }

        return $this->cache->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->cache->saveDeferred($item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return $this->cache->commit();
    }

    /**
     * A hierarchy key MUST begin with the separator.
     * @param string $key
     *
     * @return bool
     */
    private function isHierarchyKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf('Key must be string.'));
        }

        return substr($key, 0, 1) === self::HIERARCHY_SEPARATOR;
    }

    /**
     * @param string $key
     * @param array  $tags
     *
     * @return bool true if parents are valid
     */
    private function validateParents($key, array $tags)
    {
        $parts = $this->explodeKey($key);
        $parentKey = '';
        foreach ($parts as $part) {
            $parentKey .= $part;
            if (!$this->cache->hasItem($parentKey, $tags)) {
                // Invalid item
                return false;
            }
        }

        return true;
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return array
     */
    private function explodeKey($key)
    {
        $parts = explode(self::HIERARCHY_SEPARATOR, $key);

        unset($parts[0]);
        foreach ($parts as &$part) {
            $part = self::HIERARCHY_SEPARATOR.$part;
        }

        return $parts;
    }
}
