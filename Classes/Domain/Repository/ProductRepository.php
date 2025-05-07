<?php
namespace ThomasPaul\Shopware6Api\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class ProductRepository extends Repository
{
    /**
     * Findet ein Produkt anhand der Shopware-ID.
     */
    public function findOneByShopwareId(string $shopwareId)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('shopwareId', $shopwareId)
        );
        return $query->execute()->getFirst();
    }
} 