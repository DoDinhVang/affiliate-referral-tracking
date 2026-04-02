<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    public function graphql(string $query, array $variables = [])
    {
        $shop = Auth::guard('shop')->user();
        $accessToken = $shop->access_token;
        $domain = $shop->domain;
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$domain}/admin/api/" . env('SHOPIFY_API_VERSION') . "/graphql.json", [
            'query' => $query,
            'variables' => empty($variables) ? null : $variables,
        ]);

        if ($response->failed()) {
            throw new \Exception('Shopify GraphQL request failed: ' . $response->body());
        }
        return $response->json();
    }

    public function getShopInfo()
    {
        $query = '{
            shop {
                name
                domain
                email
            }
        }';

        return $this->graphql($query);
    }
}
