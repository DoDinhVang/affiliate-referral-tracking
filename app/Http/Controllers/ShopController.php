<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ShopInterface;

class ShopController extends Controller
{
    protected $shopRepository;

    public function __construct(ShopInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function get()
    {
        return $this->shopRepository->all();
    }
}
