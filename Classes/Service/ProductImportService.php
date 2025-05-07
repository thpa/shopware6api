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
    protected const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

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

                // Import images
                if (!empty($item['media'])) {
                    $images = new ObjectStorage();
                    foreach ($item['media'] as $media) {
                        if (!empty($media['media']['url'])) {
                            $fileReference = $this->createFileReferenceFromUrl(
                                $media['media']['url'],
                                $item['translated']['name'] ?? '',
                                $media['media']['name'] ?? ''
                            );
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

    protected function createFileReferenceFromUrl(string $url, string $productName, string $mediaName): ?FileReference
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }

            // Create temp file
            $tempPath = GeneralUtility::getFileAbsFileName('typo3temp/' . uniqid('shopware_import_') . '_' . basename($url));
            
            // Download file
            $imageContent = @file_get_contents($url);
            if (!$imageContent) {
                throw new \RuntimeException('Could not download image from URL');
            }
            file_put_contents($tempPath, $imageContent);

            // Get mime type
            $mimeType = mime_content_type($tempPath);
            if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
                throw new \RuntimeException('Invalid image type: ' . $mimeType);
            }

            // Get storage and ensure import folder exists
            $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
            if (!$storage->hasFolder(self::IMPORT_FOLDER)) {
                $storage->createFolder(self::IMPORT_FOLDER);
            }

            // Import file
            $fileImporter = GeneralUtility::makeInstance(FileImporter::class);
            $uploadedFile = new UploadedFile(
                $tempPath,
                $mimeType,
                UPLOAD_ERR_OK,
                $this->sanitizeFileName(basename($url)),
                true
            );
            
            $file = $fileImporter->import($uploadedFile, self::IMPORT_FOLDER, 'autoRename', $storage);
            
            if (!$file instanceof FileInterface) {
                throw new \RuntimeException('File import failed');
            }

            // Create file reference
            $fileReference = GeneralUtility::makeInstance(FileReference::class);
            $fileReference->setOriginalResource($file);
            
            // Set metadata
            $fileReference->setTitle($mediaName ?: $productName);
            $fileReference->setDescription('Imported from Shopware for product: ' . $productName);
            $fileReference->setAlternative($mediaName ?: $productName);

            return $fileReference;
        } catch (\Exception $e) {
            // Log error
            GeneralUtility::devLog(
                'Error importing image: ' . $e->getMessage(),
                'shopware6api',
                3,
                [
                    'url' => $url,
                    'product' => $productName,
                    'error' => $e->getMessage()
                ]
            );
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