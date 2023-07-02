<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Core\Content\Product;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductCustomerPriceDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'md_product_customer_price';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductCustomerPriceEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductCustomerPriceCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FloatField('net', 'net'))->addFlags(new Required()),
            (new FloatField('gross', 'gross'))->addFlags(new Required()),
            new FloatField('list_net', 'listNet'),
            new FloatField('list_gross', 'listGross'),
            (new FkField('product_id', 'productId', ProductDefinition::class))
                ->addFlags(new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))
                ->addFlags(new Required()),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class),
            new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class),
        ]);
    }
}
