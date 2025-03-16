<?php

namespace CmsHealthProject\Event;

use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use Symfony\Contracts\EventDispatcher\Event;

class GatherHealthEvent extends Event
{
    public const NAME = 'CmsHealthProject.GatherHealthEvent';

    private CheckCollection $checkCollection;

    /**
     * @var string[]|null
     */
    private array $names;

    /**
     * @param null|string[] $names
     */
    public function __construct(array $names)
    {
        $this->checkCollection = new CheckCollection();
        $this->names           = $names;
    }

    public function getNames(): ?array
    {
        return $this->names;
    }

    public function getCheckCollection(): CheckCollection
    {
        return $this->checkCollection;
    }
}
