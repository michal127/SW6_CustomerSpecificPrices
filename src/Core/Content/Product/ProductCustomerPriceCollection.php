<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Core\Content\Product;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductCustomerPriceCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCustomerPriceEntity::class;
    }
}
