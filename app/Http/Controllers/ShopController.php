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

    public function getShopifyAccessToken()
    {
        $accessToken = $this->shopifyService->getAccessToken();
        return $this->success('Access token retrieved successfully.', ['access_token' => $accessToken]);
    }
    public function getShopifyProducts()
    {
        $query =
            'query GetProducts {
                products(first: 10) {
                    nodes {
                    id
                    title
                    }
                }
            }';
        $response = $this->shopifyService->graphql($query);
        return $this->success('Products retrieved successfully.', $response);
    }
    public function getShopifyAccessScopes()
    {
        $response = $this->shopifyService->getAccessScopes();
        return $this->success('Access scopes retrieved successfully.', $response);
    }
}
