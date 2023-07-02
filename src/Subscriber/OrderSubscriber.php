<?php

declare(strict_types=1);

namespace MD\CustomerSpecificPrices\Subscriber;

use MD\CustomerSpecificPrices\Message\CustomerProductPriceDecreaseMessage;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onOrderPlaced'
        ];
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $this->messageBus->dispatch(
            new CustomerProductPriceDecreaseMessage($event->getOrderId(), $event->getContext())
        );
    }
}
