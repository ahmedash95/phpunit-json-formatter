<?php

namespace Ahmedash95\PhpunitJsonFormatter;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionFinished;

require_once __DIR__ . '/Subscribers.php';

final class JsonExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters
    ): void {
        ini_set('display_errors', '1');

        // Disable PHPUnit's progress output
        if (PHP_SAPI === 'cli') {
            // Suppress PHPUnit's standard output by replacing the stdout
            $this->suppressStandardOutput();
        }

        // Create our subscriber handler
        $handler = new JsonSubscriberHandler();

        // Register subscribers via the provided facade
        $facade->registerSubscriber(new TestSuiteStartedSubscriber($handler));
        $facade->registerSubscriber(new TestSuiteFinishedSubscriber($handler));
        $facade->registerSubscriber(new TestPreparedSubscriber($handler));
        $facade->registerSubscriber(new TestFinishedSubscriber($handler));
        $facade->registerSubscriber(new TestPassedSubscriber($handler));
        $facade->registerSubscriber(new TestFailedSubscriber($handler));
        $facade->registerSubscriber(new TestSkippedSubscriber($handler));

        // Register execution subscribers to handle start/end of the test run
        $facade->registerSubscriber(new class($handler) implements ExecutionStartedSubscriber {
            private JsonSubscriberHandler $handler;

            public function __construct(JsonSubscriberHandler $handler)
            {
                $this->handler = $handler;
            }

            public function notify(ExecutionStarted $event): void
            {
                // Suppress PHPUnit's initial output
            }
        });

        $facade->registerSubscriber(new class($handler) implements ExecutionFinishedSubscriber {
            private JsonSubscriberHandler $handler;

            public function __construct(JsonSubscriberHandler $handler)
            {
                $this->handler = $handler;
            }

            public function notify(ExecutionFinished $event): void
            {
                // Nothing to do here, the test suite finished subscriber will handle output
            }
        });
    }

    /**
     * Suppress standard output to prevent PHPUnit's default output
     */
    private function suppressStandardOutput(): void
    {
        // Create a custom output buffer that captures PHPUnit's output
        ob_start(function ($buffer) {
            // Only allow output that looks like our JSON
            if (strpos($buffer, '{') === 0 && substr(trim($buffer), -1) === '}') {
                return $buffer;
            }
            // Suppress all other output
            return '';
        });
    }
}
