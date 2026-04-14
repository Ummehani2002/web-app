<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class D365CompanyService
{
    public function fetchCompanies(): array
    {
        $token = $this->getAccessToken();

        $baseUrl = rtrim((string) config('services.d365.base_url'), '/');
        $companiesPath = (string) config('services.d365.companies_path', '/companies');

        if (empty($baseUrl)) {
            throw new RuntimeException('D365 base URL is not configured.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->get($baseUrl . $companiesPath);

        if ($response->failed()) {
            throw new RuntimeException('D365 companies API failed: ' . $response->status());
        }

        $payload = $response->json();
        $records = $payload['value'] ?? $payload;

        if (!is_array($records)) {
            return [];
        }

        $normalized = [];

        foreach ($records as $record) {
            if (!is_array($record)) {
                continue;
            }

            $d365Id = $record['id']
                ?? $record['company']
                ?? $record['dataAreaId']
                ?? null;

            $name = $record['name']
                ?? $record['companyName']
                ?? $record['company']
                ?? null;

            if (!$d365Id || !$name) {
                continue;
            }

            $normalized[] = [
                'd365_id' => (string) $d365Id,
                'name' => (string) $name,
            ];
        }

        return $normalized;
    }

    protected function getAccessToken(): string
    {
        $tenantId = (string) config('services.d365.tenant_id');
        $clientId = (string) config('services.d365.client_id');
        $clientSecret = (string) config('services.d365.client_secret');
        $scope = (string) config('services.d365.scope');

        if (!$tenantId || !$clientId || !$clientSecret || !$scope) {
            throw new RuntimeException('D365 credentials are not fully configured.');
        }

        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => $scope,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to get Azure access token: ' . $response->status());
        }

        $accessToken = $response->json('access_token');

        if (!$accessToken) {
            throw new RuntimeException('Azure token response did not include access_token.');
        }

        return $accessToken;
    }
}
