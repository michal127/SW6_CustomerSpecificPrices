<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Message;

use Shopware\Core\Framework\Context;

class CustomerProductPriceDecreaseMessage
{
    public function __construct(
        private readonly string $orderId,
        private readonly Context $context
    ) {
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
