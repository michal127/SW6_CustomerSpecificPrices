<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1688296166CreateProductCustomerPriceTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1688296166;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `md_product_customer_price` (
                `id`                 BINARY(16) NOT NULL,
                `product_id`         BINARY(16) NOT NULL,
                `customer_id`        BINARY(16) NOT NULL,
                `net`                FLOAT NOT NULL,
                `gross`              FLOAT NOT NULL,
                `list_net`           FLOAT NULL,
                `list_gross`         FLOAT  NULL,
                `created_at`         DATETIME(3) NOT NULL,
                `updated_at`         DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY(`product_id`, `customer_id`),
                KEY `fk.md_product_customer_price.product_id` (`product_id`),
                KEY `fk.md_product_customer_price.customer_id` (`customer_id`),
                CONSTRAINT `fk.md_product_customer_price.product` 
                    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.md_product_customer_price.customer` 
                    FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
