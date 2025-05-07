<?php
namespace ThomasPaul\Shopware6Api\Domain\Model;

class Product
{
    protected int $uid = 0;
    protected string $shopwareId = '';
    protected string $name = '';
    protected string $description = '';
    protected float $price = 0.0;
    protected bool $isActive = true;

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function getShopwareId(): string
    {
        return $this->shopwareId;
    }

    public function setShopwareId(string $shopwareId): void
    {
        $this->shopwareId = $shopwareId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}