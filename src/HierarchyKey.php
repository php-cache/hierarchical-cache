<?php

namespace Cache\Hierarchy;

final class HierarchyKey
{
    const SEPARATOR = '|';

    private $key;
    private $keySeparator;

    /**
     * @param string $key
     * @param string $keySeparator
     */
    public function __construct($key, $keySeparator)
    {
        $this->key = $key;
        $this->keySeparator = $keySeparator;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return substr($this->key, 0, 1) === self::SEPARATOR;
    }

    /**
     * @return array
     */
    public function iterator()
    {
        list($key, $tag) = explode($this->keySeparator, $this->key.$this->keySeparator.$this->keySeparator);
        $parts = explode(HierarchyKey::SEPARATOR, $key);
        unset($parts[0]);

        return array_map(function ($a) use ($tag) {return $a.':'.$tag;},$parts);
    }
}
