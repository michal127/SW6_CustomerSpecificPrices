<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Core\Content\Product;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductCustomerPriceEntity extends Entity
{
    use EntityIdTrait;

    protected float $net;

    protected float $gross;

    protected ?float $listNet = null;

    protected ?float $listGross = null;

    protected string $customerId;

    protected string $productId;

    protected ?ProductEntity $product = null;

    protected ?CustomerEntity $customer = null;

    public function getNet(): float
    {
        return $this->net;
    }

    public function setNet(float $net): void
    {
        $this->net = $net;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function setGross(float $gross): void
    {
        $this->gross = $gross;
    }

    public function getListNet(): ?float
    {
        return $this->listNet;
    }

    public function setListNet(?float $listNet): void
    {
        $this->listNet = $listNet;
    }

    public function getListGross(): ?float
    {
        return $this->listGross;
    }

    public function setListGross(?float $listGross): void
    {
        $this->listGross = $listGross;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }
}
