<?php

namespace CmsHealthProject\Service;

use CmsHealthProject\Event\GatherHealthEvent;
use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HealthProviderService
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getHealthData(array $names): CheckCollection
    {
        $event = new GatherHealthEvent($names);
        $this->eventDispatcher->dispatch($event, GatherHealthEvent::NAME);

        return $event->getCheckCollection();
    }
}
