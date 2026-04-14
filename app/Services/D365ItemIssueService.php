<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class D365ItemIssueService
{
    public function lookupItems(string $dataAreaId, ?string $itemId = null): array
    {
        $payload = [
            'DataAreaId' => $dataAreaId,
            'ItemId' => $itemId ?? '',
        ];

        return $this->postToConfiguredPath('item_lookup_path', $payload);
    }

    public function lookupProjects(string $dataAreaId, ?string $projectId = null): array
    {
        $payload = [
            'DataAreaId' => $dataAreaId,
            'ProjectId' => $projectId ?? '',
        ];

        return $this->postToConfiguredPath('project_lookup_path', $payload);
    }

    public function postItemIssue(array $payload): array
    {
        return $this->postToConfiguredPath('item_issue_post_path', $payload);
    }

    protected function postToConfiguredPath(string $pathConfigKey, array $payload): array
    {
        $token = $this->getAccessToken();
        $baseUrl = rtrim((string) config('services.d365.base_url'), '/');
        $path = (string) config("services.d365.{$pathConfigKey}");

        if ($baseUrl === '') {
            throw new RuntimeException('D365 base URL is not configured.');
        }

        if ($path === '') {
            throw new RuntimeException("D365 endpoint path is missing: {$pathConfigKey}");
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post($baseUrl . '/' . ltrim($path, '/'), $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'D365 API failed with status ' . $response->status() . ': ' . $response->body()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : ['raw' => $response->body()];
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
