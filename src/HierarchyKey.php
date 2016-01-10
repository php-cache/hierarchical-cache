<?php

namespace Cache\Hierarchy;

use Exception;

final class HierarchyKey implements HierarchyKeyInterface
{
    const SEPARATOR = '|';
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
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
        if (!$this->isValid()) {
            new Exception(sprintf('%s is not a valid HierarchyKey', $this->key));
        }

        return array_splice(explode(self::SEPARATOR, $this->key), 1);
    }
}
