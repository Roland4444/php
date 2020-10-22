<?php

namespace StorageTest\Controller\Mock;

trait MockHasAccess
{
    public function hasAccess(string $className, string $permission): bool
    {
        return true;
    }
}
