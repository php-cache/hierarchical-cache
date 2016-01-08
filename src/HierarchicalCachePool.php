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

use Cache\Taggable\TaggablePoolInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

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

    public function getItem($key, array $tags = [])
    {
        // TODO: Implement getItem() method.
    }

    public function getItems(array $keys = [], array $tags = [])
    {
        // TODO: Implement getItems() method.
    }

    public function hasItem($key, array $tags = [])
    {
        // TODO: Implement hasItem() method.
    }

    public function clear(array $tags = [])
    {
        // TODO: Implement clear() method.
    }

    public function deleteItem($key, array $tags = [])
    {
        // TODO: Implement deleteItem() method.
    }

    public function deleteItems(array $keys, array $tags = [])
    {
        // TODO: Implement deleteItems() method.
    }

    public function save(CacheItemInterface $item)
    {
        // TODO: Implement save() method.
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO: Implement saveDeferred() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * A hierarchy key MUST begin with the separator.
     * @param string $key
     *
     * @return bool
     */
    private function isHierarchyKey($key)
    {
        return substr($key, 0, 1) === self::SEPARATOR;
    }
}
