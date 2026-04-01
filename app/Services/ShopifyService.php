<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopifyService
{
    protected $clientId;
    protected $clientSecret;
    protected $shop;
    protected $shopifyApiVersion;

    public function __construct()
    {
        $this->clientId = env('SHOPIFY_CLIENT_ID');
        $this->clientSecret = env('SHOPIFY_CLIENT_SECRET');
        $this->shop = env('SHOPIFY_SHOP');
        $this->shopifyApiVersion = env('SHOPIFY_API_VERSION');
    }

    public function getAccessToken()
    {
        $accessToken = Cache::get('shopify_access_token');
        if ($accessToken) {
            return $accessToken;
        }
        $response = Http::post("https://{$this->shop}.myshopify.com/admin/oauth/access_token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get access token from Shopify: ' . $response->body());
        }
        $data = $response->json();
        Cache::put('shopify_access_token', $data['access_token'], now()->addSeconds($data['expires_in'] - 60));
        return $data['access_token'];
    }

    public function graphql(string $query, array $variables = [])
    {
        Log::info("check variable is empty", ['variables' => empty($variables)]);
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post("https://{$this->shop}.myshopify.com/admin/api/{$this->shopifyApiVersion}/graphql.json", [
            'query' => $query,
            'variables' => empty($variables) ? null : $variables,
        ]);

        if ($response->failed()) {
            throw new \Exception('Shopify GraphQL request failed: ' . $response->body());
        }
        return $response->json();
    }
    public function getAccessScopes()
    {
        $query = '
        query {
            currentAppInstallation {
                accessScopes {
                description
                handle
                }
            }
        }
        ';
        $response = $this->graphql($query);
        return $response;
    }
}
