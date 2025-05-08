<?php
namespace ThomasPaul\Shopware6Api\Service;

use ThomasPaul\Shopware6Api\Domain\Repository\ProductRepository;
use ThomasPaul\Shopware6Api\Domain\Model\Product;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Resource\Import\FileImporter;
use TYPO3\CMS\Core\Http\UploadedFile;

class ProductImportService
{
    protected const IMPORT_FOLDER = 'imported/';
    protected const ALLOWED_IMAGE_TYPES = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct(
        protected ShopwareApiService $apiService,
        protected ProductRepository $productRepository,
        protected PersistenceManager $persistenceManager
    ) {}

    public function import(): void
    {
        $products = $this->apiService->fetchProducts();
        $storagePid = (int)($this->settings['storagePid'] ?? 0);

        foreach ($products as $item) {
            $existing = $this->productRepository->findOneByShopwareId($item['id']);

            if (!$existing) {
                $product = new Product();
                $product->setShopwareId($item['id']);
                $product->setName($item['translated']['name'] ?? '');
                $product->setDescription($item['translated']['description'] ?? '');
                $product->setPrice((float)($item['calculatedPrice']['unitPrice'] ?? 0));
                $product->setIsActive((bool)($item['active'] ?? false));

                // ✅ Cover-Bild laden und setzen
                if (!empty($item['cover']['media']['url'])) {
                    $coverUrl = $item['cover']['media']['url'];

                    $coverImage = $this->createFileReferenceFromUrl($coverUrl, $storagePid);
                    if ($coverImage) {
                        $product->setCoverImage($coverImage);
                    }
                }

                // Import all images
                if (!empty($item['media'])) {
                    $images = new ObjectStorage();
                    foreach ($item['media'] as $media) {
                        if (!empty($media['media']['url'])) {
                            $fileReference = $$this->createFileReferenceFromUrl($url, $name, $mediaName, 'images');
                            if ($fileReference) {
                                $images->attach($fileReference);
                            }
                        }
                    }
                    $product->setImages($images);
                }

                $this->productRepository->add($product);
            } else {
                // Optional: Update vorhandener Datensatz
            }
        }

        $this->persistenceManager->persistAll();
    }

    protected function createFileReferenceFromUrl(string $url, int $pid): ?FileReference
    {
        try {
            $fileName = basename(parse_url($url, PHP_URL_PATH));
            $tempPath = GeneralUtility::getFileAbsFileName('typo3temp/' . uniqid() . '_' . $fileName);
            file_put_contents($tempPath, file_get_contents($url));

            $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
            $folder = $storage->hasFolder('imported') ? $storage->getFolder('imported') : $storage->createFolder('imported');

            $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
            $file = $storage->addFile($tempPath, $folder, $safeName);

            $ref = GeneralUtility::makeInstance(FileReference::class);
            $ref->setFile($file);
            $ref->setPid($pid);

            // Diese 3 Zeilen sind entscheidend:
            $ref->_setProperty('uidLocal', $file->getUid());
            $ref->_setProperty('tablenames', 'tx_shopware6api_domain_model_product');
            $ref->_setProperty('fieldname', 'cover_image');

            return $ref;
        } catch (\Throwable $e) {
            return null;
        } finally {
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    protected function sanitizeFileName(string $fileName): string
    {
        // Remove special characters and spaces
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
        // Ensure unique filename
        return uniqid() . '_' . $fileName;
    }
}