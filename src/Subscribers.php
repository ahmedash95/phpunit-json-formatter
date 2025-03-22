<?php

namespace Ahmedash95\PhpunitJsonFormatter;

use PHPUnit\Event\TestSuite\Started as TestSuiteStarted;
use PHPUnit\Event\TestSuite\StartedSubscriber as TestSuiteStartedSubscriberInterface;
use PHPUnit\Event\TestSuite\Finished as TestSuiteFinished;
use PHPUnit\Event\TestSuite\FinishedSubscriber as TestSuiteFinishedSubscriberInterface;
use PHPUnit\Event\Test\Prepared as TestPrepared;
use PHPUnit\Event\Test\PreparedSubscriber as TestPreparedSubscriberInterface;
use PHPUnit\Event\Test\Finished as TestFinished;
use PHPUnit\Event\Test\FinishedSubscriber as TestFinishedSubscriberInterface;
use PHPUnit\Event\Test\Passed as TestPassed;
use PHPUnit\Event\Test\PassedSubscriber as TestPassedSubscriberInterface;
use PHPUnit\Event\Test\Failed as TestFailed;
use PHPUnit\Event\Test\FailedSubscriber as TestFailedSubscriberInterface;
use PHPUnit\Event\Test\Skipped as TestSkipped;
use PHPUnit\Event\Test\SkippedSubscriber as TestSkippedSubscriberInterface;

// Test Suite Started Subscriber
class TestSuiteStartedSubscriber implements TestSuiteStartedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestSuiteStarted $event): void
    {
        $this->handler->handleTestSuiteStarted($event);
    }
}

// Test Suite Finished Subscriber
class TestSuiteFinishedSubscriber implements TestSuiteFinishedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestSuiteFinished $event): void
    {
        $this->handler->handleTestSuiteFinished($event);
    }
}

// Test Prepared Subscriber
class TestPreparedSubscriber implements TestPreparedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestPrepared $event): void
    {
        $this->handler->handleTestPrepared($event);
    }
}

// Test Finished Subscriber
class TestFinishedSubscriber implements TestFinishedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestFinished $event): void
    {
        $this->handler->handleTestFinished($event);
    }
}

// Test Passed Subscriber
class TestPassedSubscriber implements TestPassedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestPassed $event): void
    {
        $this->handler->handleTestPassed($event);
    }
}

// Test Failed Subscriber
class TestFailedSubscriber implements TestFailedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestFailed $event): void
    {
        $this->handler->handleTestFailed($event);
    }
}

// Test Skipped Subscriber
class TestSkippedSubscriber implements TestSkippedSubscriberInterface
{
    private JsonSubscriberHandler $handler;

    public function __construct(JsonSubscriberHandler $handler)
    {
        $this->handler = $handler;
    }

    public function notify(TestSkipped $event): void
    {
        $this->handler->handleTestSkipped($event);
    }
}
