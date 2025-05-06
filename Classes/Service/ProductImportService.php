<?php
namespace ThomasPaul\Shopware6Api\Service;

use ThomasPaul\Shopware6Api\Domain\Repository\ProductRepository;
use ThomasPaul\Shopware6Api\Domain\Model\Product;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductImportService
{
    public function __construct(
        protected ShopwareApiService $apiService,
        protected ProductRepository $productRepository,
        protected PersistenceManager $persistenceManager
    ) {}

    public function import(): void
    {
        $products = $this->apiService->fetchProducts();

        foreach ($products as $item) {
            $existing = $this->productRepository->findOneByShopwareId($item['id']);

            if (!$existing) {
                $product = new Product();
                $product->setShopwareId($item['id']);
                $product->setName($item['translated']['name'] ?? '');
                $product->setDescription($item['translated']['description'] ?? '');
                $product->setPrice((float)($item['price'][0]['gross'] ?? 0));
                $product->setIsActive((bool)($item['active'] ?? true));

                $this->productRepository->add($product);
            } else {
                // Optional: Update vorhandener Datensatz
            }
        }

        $this->persistenceManager->persistAll();
    }
}