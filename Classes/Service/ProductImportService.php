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

        foreach ($products as $item) {
            $existing = $this->productRepository->findOneByShopwareId($item['id']);

            if (!$existing) {
                $product = new Product();
                $product->setShopwareId($item['id']);
                $product->setName($item['translated']['name'] ?? '');
                $product->setDescription($item['translated']['description'] ?? '');
                $product->setPrice((float)($item['calculatedPrice']['unitPrice'] ?? 0));
                $product->setIsActive((bool)($item['active'] ?? false));

                // Import cover image
                if (!empty($item['cover']['media']['url'])) {
                    $coverUrl = $item['cover']['media']['url'];
                    $coverName = $item['cover']['media']['fileName'] ?? ($item['translated']['name'] ?? '');

                    $coverReference = $this->createFileReferenceFromUrl(
                        $coverUrl,
                        $item['translated']['name'] ?? '',
                        $coverName
                    );

                    if ($coverReference) {
                        $product->setCoverImage($coverReference);// Voraussetzung: Es gibt $image im Model
                    }
                }

                // Import all images
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

    protected function createFileReferenceFromUrl(string $url, string $productName, string $mediaName): ?\TYPO3\CMS\Extbase\Domain\Model\FileReference
    {
        try {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }

            $tempPath = GeneralUtility::getFileAbsFileName('typo3temp/' . uniqid('shopware_', true) . '_' . basename($url));
            $imageContent = @file_get_contents($url);
            if (!$imageContent) {
                throw new \RuntimeException('Could not download image from URL');
            }
            file_put_contents($tempPath, $imageContent);

            $mimeType = mime_content_type($tempPath);
            if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES, true)) {
                throw new \RuntimeException('Invalid image type: ' . $mimeType);
            }

            $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
            $folder = $storage->getFolder(self::IMPORT_FOLDER);
            if (!$storage->hasFolder(self::IMPORT_FOLDER)) {
                $folder = $storage->createFolder(self::IMPORT_FOLDER);
            }

            $fileName = $this->sanitizeFileName(basename(parse_url($url, PHP_URL_PATH)));
            if (!$storage->hasFile($fileName, $folder)) {
                $file = $storage->addFile($tempPath, $folder, $fileName);
            } else {
                $file = $storage->getFile($folder->getIdentifier() . $fileName);
            }

            // ⬇️ Der moderne Weg: direkt FileReference erzeugen
            $fileReference = $file->getReference();

            if ($fileReference instanceof \TYPO3\CMS\Core\Resource\FileReference) {
                $extbaseFileReference = GeneralUtility::makeInstance(FileReference::class);
                $extbaseFileReference->setOriginalResource($fileReference);

                $extbaseFileReference->setTitle($mediaName ?: $productName);
                $extbaseFileReference->setAlternative($mediaName ?: $productName);
                $extbaseFileReference->setDescription('Imported from Shopware');

                return $extbaseFileReference;
            }

            return null;
        } catch (\Throwable $e) {
            $logger = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
                ->getLogger(__CLASS__);

            $logger->error('Error importing image: ' . $e->getMessage(), [
                'url' => $url,
                'product' => $productName,
                'exception' => $e
            ]);
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