<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Decorator;

use MD\CustomerSpecificPrices\Core\Content\Product\ProductCustomerPriceEntity;
use MD\CustomerSpecificPrices\Service\CustomerPricesService;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CustomerPriceCalculator extends AbstractProductPriceCalculator
{
    public function __construct(
        private readonly AbstractProductPriceCalculator $decorated,
        private readonly CustomerPricesService $customerPricesService
    ) {
    }


    public function calculate(iterable $products, SalesChannelContext $context): void
    {
        $customer = $context->getCustomer();
        if (!$customer) {
            $this->decorated->calculate($products, $context);
            return;
        }

        $this->overrideWithCustomerSpecificPrices($products, $customer, $context);
        $this->decorated->calculate($products, $context);
    }

    public function getDecorated(): AbstractProductPriceCalculator
    {
        return $this->decorated;
    }

    private function overrideWithCustomerSpecificPrices(
        iterable $products,
        CustomerEntity $customer,
        SalesChannelContext $context
    ): void {
        $customerPrices = $this->customerPricesService->fetchCustomerPrices($customer->getId(), $context->getContext());

        foreach ($products as $product) {
            if (!$product instanceof ProductEntity) {
                continue;
            }
            $customerPrice = $customerPrices
                ->filterByProperty('productId', $product->getId())
                ->first();

            if (!$customerPrice instanceof ProductCustomerPriceEntity) {
                continue;
            }
            $price = $product->getPrice()->first();

            if (!$price instanceof Price) {
                continue;
            }

            $price->setGross($customerPrice->getGross());
            $price->setNet($customerPrice->getNet());

            $customerListGrossPrice = $customerPrice->getListGross();
            $customerListNetPrice = $customerPrice->getListNet();
            if (!empty($customerListGrossPrice) && !empty($customerListNetPrice)) {
                $listPrice = $price->getListPrice();
                if (!$listPrice) {
                    $price->setListPrice(
                        new Price(
                            $price->getCurrencyId(),
                            $customerListNetPrice,
                            $customerListGrossPrice,
                            $price->getLinked()
                        )
                    );
                } else {
                    $listPrice->setGross($customerListGrossPrice);
                    $listPrice->setNet($customerListNetPrice);
                }
            }
        }
    }
}
