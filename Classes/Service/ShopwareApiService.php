<?php
namespace ThomasPaul\Shopware6Api\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ShopwareApiService
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = $this->resolveBaseUrl();
    }

    protected function resolveBaseUrl(): string
    {
        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        return rtrim($extensionConfig->get('shopware6api')['apiBaseUrl'] ?? '', '/') . '/store-api/';
    }

    /**
     * Ruft Produkte aus der Shopware 6 API ab.
     *
     * @return array
     */
    public function fetchProducts(): array
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $response = $requestFactory->request(
            $this->apiBaseUrl . 'product',
            'GET',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        return $data['elements'] ?? [];
    }
} 