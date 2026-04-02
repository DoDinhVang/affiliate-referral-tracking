<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ShopInterface;
use App\Services\ShopifyService;

class ShopController extends Controller
{
    protected $shopRepository;
    protected $shopifyService;

    public function __construct(ShopInterface $shopRepository, ShopifyService $shopifyService)
    {
        $this->shopRepository = $shopRepository;
        $this->shopifyService = $shopifyService;
    }
}
