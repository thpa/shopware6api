<?php
namespace ThomasPaul\Shopware6Api\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ShopwareApiService
{
    protected string $apiBaseUrl;
    protected string $accessKey;

    public function __construct()
    {
        $this->apiBaseUrl = $this->resolveBaseUrl();
        $this->accessKey = $this->resolveAccessKey();
    }

    protected function resolveBaseUrl(): string
    {
        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        return rtrim($extensionConfig->get('shopware6api')['apiBaseUrl'] ?? '', '/') . '/store-api/';
    }

    protected function resolveAccessKey(): string
    {
        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        return $extensionConfig->get('shopware6api')['accessKey'] ?? '';
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
                    'sw-access-key' => $this->accessKey
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        return $data['elements'] ?? [];
    }
} 