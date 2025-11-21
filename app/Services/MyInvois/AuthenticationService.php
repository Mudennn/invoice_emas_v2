<?php

namespace App\Services\MyInvois;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthenticationService
{
    private string $baseUrl;

    private string $clientId;

    private string $clientSecret;

    private string $cacheKey = 'myinvois_access_token';

    private int $cacheMinutes = 59; // Token valid for 60 min, cache for 59 min

    public function __construct()
    {
        $this->baseUrl = config('myinvois.api.base_url');
        $this->clientId = config('myinvois.credentials.client_id');
        $this->clientSecret = config('myinvois.credentials.client_secret');
    }

    /**
     * Get current valid token (with caching)
     * Returns cached token if available and valid, otherwise authenticates
     */
    public function getToken(): string
    {
        // Check if token exists in cache
        $token = Cache::get($this->cacheKey);

        if ($token) {
            Log::channel('myinvois')->info('Using cached access token');

            return $token;
        }

        // Token not in cache, authenticate
        return $this->authenticate();
    }

    /**
     * Authenticate with MyInvois API and get OAuth token
     * Uses OAuth 2.0 client credentials flow
     */
    public function authenticate(): string
    {
        Log::channel('myinvois')->info('Authenticating with MyInvois API', [
            'client_id' => $this->clientId,
            'base_url' => $this->baseUrl,
        ]);

        try {
            $response = Http::asForm()
                ->timeout(config('myinvois.api.timeout'))
                ->post("{$this->baseUrl}/connect/token", [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'InvoicingAPI',
                ]);

            if (! $response->successful()) {
                Log::channel('myinvois')->error('Authentication failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception("Authentication failed: {$response->body()}");
            }

            $data = $response->json();
            $accessToken = $data['access_token'] ?? null;

            if (! $accessToken) {
                throw new \Exception('Access token not found in response');
            }

            // Cache the token for 59 minutes
            Cache::put($this->cacheKey, $accessToken, now()->addMinutes($this->cacheMinutes));

            Log::channel('myinvois')->info('Authentication successful, token cached');

            return $accessToken;

        } catch (\Exception $e) {
            Log::channel('myinvois')->error('Authentication exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Force refresh token (clear cache and re-authenticate)
     */
    public function refreshToken(): string
    {
        Log::channel('myinvois')->info('Force refreshing access token');

        Cache::forget($this->cacheKey);

        return $this->authenticate();
    }

    /**
     * Check if token exists and is valid in cache
     */
    public function isTokenValid(): bool
    {
        return Cache::has($this->cacheKey);
    }

    /**
     * Clear token from cache
     */
    public function clearToken(): void
    {
        Cache::forget($this->cacheKey);
        Log::channel('myinvois')->info('Access token cleared from cache');
    }
}
