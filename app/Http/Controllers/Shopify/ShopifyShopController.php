<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;

class ShopifyShopController extends Controller
{
    protected $shopifyService;
    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function getShopInfo()
    {
        return $this->shopifyService->getShopInfo();
    }
}
