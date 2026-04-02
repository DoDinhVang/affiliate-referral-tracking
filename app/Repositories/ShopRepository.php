<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\User;
use App\Repositories\Interfaces\ShopInterface;

class ShopRepository implements ShopInterface
{
    public function all()
    {
        return Shop::all();
    }

    public function getByUser(User $user)
    {
        return Shop::where('user_id', $user->id)->get();
    }

    public function findByDomain(string $domain)
    {
        return Shop::where('domain', $domain)->first();
    }

    public function createOrUpdate(array $attributes, array $values)
    {
        return Shop::updateOrCreate($attributes, $values);
    }
}
