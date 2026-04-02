<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface ShopInterface
{
    public function all();

    public function getByUser(User $user);

    public function findByDomain(string $domain);

    public function createOrUpdate(array $attributes, array $values);
}
