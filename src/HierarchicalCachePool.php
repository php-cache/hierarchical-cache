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

use Cache\Adapter\Common\Exception\InvalidArgumentException;
use Cache\Taggable\TaggablePoolInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HierarchicalCachePool implements CacheItemPoolInterface, HierarchicalPoolInterface, TaggablePoolInterface
{
    const SEPARATOR = '|';

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
        if (!$this->isHierarchyKey($key)) {
            return $this->cache->getItem($key, $tags);
        }

        // TODO: Implement getItem() method.
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
        if (!$this->isHierarchyKey($key)) {
            return $this->cache->hasItem($key, $tags);
        }
        // TODO: Implement hasItem() method.
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
        if (!$this->isHierarchyKey($key)) {
            return $this->cache->deleteItem($key, $tags);
        }
        // TODO: Implement deleteItem() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys, array $tags = [])
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->deleteItem($key, $tags);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
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

        return substr($key, 0, 1) === self::SEPARATOR;
    }
}
