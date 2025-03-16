<?php

namespace CmsHealthProject\Tests\Event;

use CmsHealth\Definition\CheckResultStatus;
use CmsHealthProject\Event\GatherHealthEvent;
use CmsHealthProject\SerializableReferenceImplementation\Check;
use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use CmsHealthProject\SerializableReferenceImplementation\CheckResult;
use DateTime;
use PHPUnit\Framework\TestCase;

class GatherHealthEventTest extends TestCase
{
    public function testConstructorInitializesProperties(): void
    {
        $names = ['check1', 'check2'];
        $event = new GatherHealthEvent($names);

        $this->assertSame($names, $event->getNames());
        $this->assertInstanceOf(CheckCollection::class, $event->getCheckCollection());
    }

    public function testConstructorWithNullNames(): void
    {
        $event = new GatherHealthEvent([]);

        $this->assertSame([], $event->getNames());
        $this->assertInstanceOf(CheckCollection::class, $event->getCheckCollection());
    }

    public function testEventConstantName(): void
    {
        $this->assertSame('CmsHealthProject.GatherHealthEvent', GatherHealthEvent::NAME);
    }

    public function testAddCheck(): void
    {
        $event = new GatherHealthEvent([]);
        $checkCollection = $event->getCheckCollection();

        $this->assertFalse($checkCollection->hasChecks());


        // $this->assertSame(json_encode(new CheckCollection()), $eventJson);

        $check = new Check('check1');
        $checkResult = new CheckResult(
            CheckResultStatus::Pass,
            '1234-1234',
            'system',
            new DateTime(),
            '0',
            'time',
            ''
        );
        $check->addCheckResults($checkResult);
        $checkCollection->addCheck($check);

        $this->assertTrue($checkCollection->hasChecks());
        $this->assertCount(1, $checkCollection->getChecks());

        $eventJson = json_encode($checkCollection, JSON_PRETTY_PRINT);
        $this->assertIsString($eventJson);
    }
}
