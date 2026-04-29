<?php

namespace App\Services;

use RuntimeException;

class D365GrnService extends D365ItemIssueService
{
    public function lookup(string $dataAreaId, string $purchId = '', string $vendName = '', string $projId = ''): array
    {
        $requestPayload = [
            'DataAreaId' => $dataAreaId,
            'PurchId'    => $purchId,
            'VendName'   => $vendName,
            'ProjId'     => $projId,
        ];

        try {
            return $this->postToConfiguredPath('grn_lookup_path', [
                '_request' => $requestPayload,
            ]);
        } catch (RuntimeException $e) {
            return $this->postToConfiguredPath('grn_lookup_path', $requestPayload);
        }
    }

    public function lookupLines(string $dataAreaId, string $purchId): array
    {
        $payload = [
            'DataAreaId' => $dataAreaId,
            'purchId'    => $purchId,
        ];

        try {
            return $this->postToConfiguredPath('grn_line_lookup_path', $payload);
        } catch (RuntimeException $e) {
            return $this->postToConfiguredPath('grn_line_lookup_path', [
                '_request' => $payload,
            ]);
        }
    }

    public function postPackingSlip(string $dataAreaId, array $header, array $lines): array
    {
        $payload = [
            '_request' => [
                'DataAreaId' => $dataAreaId,
                'PurchPackHeader' => $header,
                'PurchPackLines' => $lines,
            ],
        ];

        try {
            return $this->postToConfiguredPath('grn_post_path', $payload);
        } catch (RuntimeException $e) {
            return $this->postToConfiguredPath('grn_post_path', $payload['_request']);
        }
    }
}
