<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Message\Handler;

use MD\CustomerSpecificPrices\Message\CustomerProductPriceDecreaseMessage;
use MD\CustomerSpecificPrices\Service\CustomerPricesService;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CustomerProductPriceDecreaseHandler
{
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly CustomerPricesService $customerPricesService
    ) {
    }

    /**
     * Decreasing customer product specific price by 1% after order is performed
     */
    public function __invoke(CustomerProductPriceDecreaseMessage $message): void
    {
        $context = $message->getContext();
        $order = $this->fetchOrder($message->getOrderId(), $context);
        $customerId = $order?->getOrderCustomer()?->getCustomerId();
        $items = $order?->getLineItems();

        if (is_null($customerId) || is_null($items) || $items->count() === 0) {
            return;
        }

        $this->customerPricesService->decreasePricesForOrderLineItems(
            $items,
            $customerId,
            $order->getSalesChannelId(),
            $context
        );
    }

    private function fetchOrder(string $orderId, Context $context): ?OrderEntity
    {
        $criteria = (new Criteria([$orderId]))
            ->addAssociations(['lineItems', 'orderCustomer']);

        return $this->orderRepository->search($criteria, $context)->first();
    }
}
