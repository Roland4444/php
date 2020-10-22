<?php

namespace Core\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface IRepository extends ObjectRepository
{
    public function save(object $entity): void;

    public function merge(object $entity): void;

    public function remove(int $id): void;

    public function getReference(int $id);
}
