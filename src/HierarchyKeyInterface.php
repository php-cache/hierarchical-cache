<?php
namespace Cache\Hierarchy;

use Exception;

interface HierarchyKeyInterface
{
    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function iterator();
}
