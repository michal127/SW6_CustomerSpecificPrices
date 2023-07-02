<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Service;

use MD\CustomerSpecificPrices\Core\Content\Product\ProductCustomerPriceEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CustomerPricesService
{
    private const PRICE_DECREASE_PERCENTAGE = 0.01;
    private array $memCustomerPrices = [];

    public function __construct(
        private readonly EntityRepository    $mdProductCustomerPriceRepository,
        private readonly SystemConfigService $systemConfigService
    )
    {
    }

    public function fetchCustomerPrices(string $customerId, Context $context): EntitySearchResult
    {
        if (isset($this->memCustomerPrices[$customerId])) {
            return $this->memCustomerPrices[$customerId];
        }
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $customerPrices = $this->mdProductCustomerPriceRepository->search($criteria, $context);

        $this->memCustomerPrices[$customerId] = $customerPrices;

        return $customerPrices;
    }

    public function decreasePricesForOrderLineItems(
        OrderLineItemCollection $lineItemCollection,
        string                  $customerId,
        string                  $salesChannelId,
        Context                 $context
    ): void
    {
        $minimumPrice = $this->fetchMinimumProductPrice($salesChannelId);
        $customerPrices = $this->fetchCustomerPrices($customerId, $context);
        $updates = [];

        /** @var OrderLineItemEntity $item */
        foreach ($lineItemCollection as $item) {
            $matchingCustomerProductPrice = $customerPrices
                ->filterByProperty('productId', $item->getProductId())
                ->first();

            if (!$matchingCustomerProductPrice instanceof ProductCustomerPriceEntity) {
                continue;
            }

            $netPrice = $matchingCustomerProductPrice->getNet();
            $grossPrice = $matchingCustomerProductPrice->getGross();

            $newNetPrice = round($netPrice - ($netPrice * self::PRICE_DECREASE_PERCENTAGE), 2);
            $newGrossPrice = round($grossPrice - ($grossPrice * self::PRICE_DECREASE_PERCENTAGE), 2);

            if ($newNetPrice >= $minimumPrice && $newGrossPrice >= $minimumPrice) {
                $updates[] = [
                    'id' => $matchingCustomerProductPrice->getId(),
                    'net' => $newNetPrice,
                    'gross' => $newGrossPrice
                ];
            }
        }

        if (!empty($updates)) {
            $this->mdProductCustomerPriceRepository->update($updates, $context);
        }
    }

    private function fetchMinimumProductPrice(string $salesChannelId): int
    {
        return $this->systemConfigService->getInt(
            'CustomerSpecificPrices.config.minimumProductPriceDecrease',
            $salesChannelId
        );
    }
}
