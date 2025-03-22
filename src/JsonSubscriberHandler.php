<?php

namespace Ahmedash95\PhpunitJsonFormatter;

use PHPUnit\Event\TestSuite\Started as TestSuiteStarted;
use PHPUnit\Event\TestSuite\Finished as TestSuiteFinished;
use PHPUnit\Event\Test\Prepared as TestPrepared;
use PHPUnit\Event\Test\Finished as TestFinished;
use PHPUnit\Event\Test\Passed as TestPassed;
use PHPUnit\Event\Test\Failed as TestFailed;
use PHPUnit\Event\Test\Skipped as TestSkipped;
use ReflectionObject;

class JsonSubscriberHandler
{
    private array $testsData = [];
    private int $totalTests = 0;
    private array $testSuites = [];

    public function handleTestSuiteStarted(TestSuiteStarted $event): void
    {
        // Track the test suite hierarchy
        $this->testSuites[] = $event->testSuite()->name();

        // Only initialize on the root test suite
        if (count($this->testSuites) === 1) {
            $this->testsData = [];
            $this->totalTests = 0;
        }
    }

    public function handleTestPrepared(TestPrepared $event): void
    {
        $testId = $event->test()->id();
        $this->totalTests++;

        // Get test metadata using reflection to avoid direct method calls
        $test = $event->test();
        $testReflection = new ReflectionObject($test);

        $className = '';
        $methodName = '';

        // Try to get the class name
        if ($testReflection->hasMethod('className')) {
            $classNameMethod = $testReflection->getMethod('className');
            $classNameMethod->setAccessible(true);
            $className = $classNameMethod->invoke($test);
        } else {
            // Try to extract it from the test ID or another source
            $parts = explode('::', (string)$testId);
            if (count($parts) > 0) {
                $className = $parts[0];
            }
        }

        // Try to get the method name
        if ($testReflection->hasMethod('methodName')) {
            $methodNameMethod = $testReflection->getMethod('methodName');
            $methodNameMethod->setAccessible(true);
            $methodName = $methodNameMethod->invoke($test);
        } elseif ($testReflection->hasMethod('name')) {
            $nameMethod = $testReflection->getMethod('name');
            $nameMethod->setAccessible(true);
            $methodName = $nameMethod->invoke($test);
        } else {
            // Try to extract it from the test ID
            $parts = explode('::', (string)$testId);
            if (count($parts) > 1) {
                $methodName = $parts[1];
            }
        }

        $this->testsData[$testId] = [
            'status'     => 'unknown',
            'id'         => $testId,
            'className'  => $className,
            'methodName' => $methodName,
            'message'    => null,
            'trace'      => [],
            'time'       => 0.0,
            'startedAt'  => microtime(true),
        ];
    }

    public function handleTestPassed(TestPassed $event): void
    {
        $testId = $event->test()->id();
        if (isset($this->testsData[$testId])) {
            $this->testsData[$testId]['status'] = 'passed';
        }
    }

    public function handleTestFailed(TestFailed $event): void
    {
        $testId = $event->test()->id();
        if (isset($this->testsData[$testId])) {
            $throwable = $event->throwable();
            $this->testsData[$testId]['status']  = 'failed';
            $this->testsData[$testId]['message'] = $throwable->message();
            $this->testsData[$testId]['trace']   = explode("\n", $throwable->stackTrace());
        }
    }

    public function handleTestSkipped(TestSkipped $event): void
    {
        $testId = $event->test()->id();
        if (isset($this->testsData[$testId])) {
            $this->testsData[$testId]['status']  = 'skipped';
            $this->testsData[$testId]['message'] = $event->message();
        }
    }

    public function handleTestFinished(TestFinished $event): void
    {
        $testId = $event->test()->id();
        if (!isset($this->testsData[$testId])) {
            return;
        }

        $this->testsData[$testId]['time'] = round(
            microtime(true) - $this->testsData[$testId]['startedAt'],
            4
        );

        if ($this->testsData[$testId]['status'] === 'unknown') {
            $this->testsData[$testId]['status'] = 'passed';
        }
    }

    public function handleTestSuiteFinished(TestSuiteFinished $event): void
    {
        // Remove this test suite from the stack
        array_pop($this->testSuites);

        // Only output JSON when the root test suite finishes
        if (empty($this->testSuites)) {
            $summary = [
                'totalTests' => $this->totalTests,
                'passed'     => count(array_filter($this->testsData, fn($t) => $t['status'] === 'passed')),
                'failed'     => count(array_filter($this->testsData, fn($t) => $t['status'] === 'failed')),
                'skipped'    => count(array_filter($this->testsData, fn($t) => $t['status'] === 'skipped')),
            ];

            $output = [
                'summary' => $summary,
                'tests'   => array_values($this->testsData),
            ];

            echo json_encode($output, JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }
}
