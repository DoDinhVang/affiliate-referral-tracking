<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ShopInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopifyAuthController extends Controller
{
    protected $shopRepository;
    protected $clientId;
    protected $clientSecret;
    protected $scopes;
    protected $redirectUri;

    public function __construct(ShopInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * Handle Shopify app installation request and redirect to OAuth authorization page.
     *
     * Flow:
     * 1. Get 'shop' and 'hmac' from query params
     * 2. Validate shop domain
     * 3. Verify HMAC to ensure request is from Shopify
     * 4. Check if shop already exists in database
     *    - If exists → redirect to home
     *    - If not → continue OAuth flow
     * 5. Redirect user to Shopify OAuth authorization URL
     *
     * Example:
     * Request from Shopify:
     * GET /install?shop=test.myshopify.com&hmac=abc123
     *
     * Step-by-step:
     * - shop = "test.myshopify.com"
     * - hmac = "abc123"
     *
     * Verify HMAC:
     * - Ensure request is valid and not tampered
     *
     * If shop NOT installed:
     * Redirect to:
     * https://test.myshopify.com/admin/oauth/authorize?
     *   client_id=your_client_id
     *   &scope=read_products,write_orders
     *   &redirect_uri=https://your-app.com/callback
     *
     * After redirect:
     * - User approves app on Shopify
     * - Shopify redirects back to /callback with 'code'
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function install(Request $request)
    {
        $shop = $request->query('shop');
        $host = $request->query('host');
        $hmac = $request->query('hmac');

        if (!$shop || !$this->isValidShopDomain($shop)) {
            return response('Missing or invalid shop parameter', 400);
        }

        // Verify the HMAC signature to ensure the request is from Shopify
        if (!$hmac || !$this->verifyShopifyHmac($request->query(), $hmac)) {
            return response('Invalid Shopify request signature', 400);
        }

        if ($this->shopRepository->findByDomain($shop)) {
            return redirect()->route('home', ['shop' => $shop, 'host' => $host]);
        }

        $authUrl = "https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id' => env('SHOPIFY_CLIENT_ID'),
            'scope' => env('SHOPIFY_SCOPES'),
            'redirect_uri' => env('SHOPIFY_REDIRECT_URI'),
        ]);

        return redirect($authUrl);
    }

    /**
     * Handle Shopify OAuth callback to exchange authorization code for access token.
     *
     * Flow:
     * 1. Get 'shop' and 'code' from query params
     * 2. Validate required params and shop domain
     * 3. Send POST request to Shopify to exchange 'code' for 'access_token'
     * 4. Validate response and extract access token
     * 5. Save or update shop data in database
     *
     * Example:
     * Request from Shopify:
     * GET /callback?shop=test.myshopify.com&code=abc123
     *
     * Step-by-step:
     * - shop = "test.myshopify.com"
     * - code = "abc123"
     *
     * Call Shopify API:
     * POST https://test.myshopify.com/admin/oauth/access_token
     * Body:
     * {
     *   client_id: "your_client_id",
     *   client_secret: "your_client_secret",
     *   code: "abc123"
     * }
     *
     * Response:
     * {
     *   "access_token": "shpat_xxx",
     *   "scope": "read_products,write_orders"
     * }
     *
     * After processing:
     * - access_token = "shpat_xxx"
     * - scope = ["read_products", "write_orders"]
     *
     * Save to DB:
     * {
     *   domain: "test.myshopify.com",
     *   access_token: "shpat_xxx",
     *   scope: ["read_products", "write_orders"],
     *   installed_at: now()
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        Log::info("Call back running");
        $shop = $request->query('shop');
        $code = $request->query('code');
        $host = $request->query('host');

        if (!$shop || !$code) {
            return response('Missing required parameters', 400);
        }

        if (!$this->isValidShopDomain($shop)) {
            return response('Invalid shop domain', 400);
        }

        $response = Http::post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => env('SHOPIFY_CLIENT_ID'),
            'client_secret' => env('SHOPIFY_CLIENT_SECRET'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            return response('Failed to exchange code for access token', 500);
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            return response('Invalid token response from Shopify', 500);
        }

        $accessToken = $data['access_token'];
        $scope = explode(',', $data['scope'] ?? env('SHOPIFY_SCOPES'));

        $this->shopRepository->createOrUpdate(
            ['domain' => $shop],
            [
                'access_token' => $accessToken,
                'scope' => $scope,
                'installed_at' => now(),
            ]
        );
        return redirect()->route('home', ['shop' => $shop, 'host' => $host]);
    }

    private function isValidShopDomain($shop)
    {
        return (bool)preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/', $shop);
    }

    /**
     * Verify Shopify HMAC to ensure the request is authentic and not tampered with.
     *
     * Flow:
     * 1. Remove 'hmac' from query params
     * 2. Sort remaining params by key
     * 3. Build query string: key=value&key2=value2
     * 4. Generate HMAC using SHA256 and app secret
     * 5. Compare with Shopify's HMAC
     *
     * Example:
     * Input:
     * $queryParams = [
     *   'shop' => 'test.myshopify.com',
     *   'timestamp' => '1710000000',
     *   'hmac' => '6f1c2abc'
     * ];
     *
     * After processing:
     * - Remove hmac:
     *   ['shop' => 'test.myshopify.com', 'timestamp' => '1710000000']
     *
     * - Build string:
     *   "shop=test.myshopify.com&timestamp=1710000000"
     *
     * - Generate:
     *   hash_hmac('sha256', string, SHOPIFY_CLIENT_SECRET) => "6f1c2abc"
     *
     * - Compare:
     *   computed === hmac => true (valid request)
     *
     * @param array  $queryParams All query parameters from the request
     * @param string $hmac        HMAC provided by Shopify
     *
     * @return bool True if valid, false otherwise
     */
    private function verifyShopifyHmac(array $queryParams, string $hmac): bool
    {
        $normalized = $queryParams;
        unset($normalized['hmac'], $normalized['signature']);
        ksort($normalized);

        $pairs = [];
        foreach ($normalized as $key => $value) {
            $pairs[] = "{$key}=" . $value;
        }

        $computed = hash_hmac('sha256', implode('&', $pairs), env('SHOPIFY_CLIENT_SECRET'));

        return hash_equals($computed, $hmac);
    }
}
