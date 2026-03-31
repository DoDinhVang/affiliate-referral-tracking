<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\ShopInterface;

class ShopRepository implements ShopInterface
{
    public function all() {}

    public function getByUser(User $user)
    {
        return null;
    }
}
