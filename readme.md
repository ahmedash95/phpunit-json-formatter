# PHPUnit JSON Formatter

A PHP library that formats PHPUnit test results as JSON, making them easier to parse and integrate with other tools.

## Requirements

- PHP 8.1 or higher
- PHPUnit 12.0.9 or higher

## Installation

```bash
composer require ahmedash95/phpunit-json-formatter
```

## Usage

To use this extension, you need to register it in your PHPUnit configuration:

### XML Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/12.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <!-- ... other PHPUnit configuration ... -->

    <extensions>
        <bootstrap class="Ahmedash95\PhpunitJsonFormatter\JsonExtension" />
    </extensions>
</phpunit>
```

### PHP Configuration

```php
use PHPUnit\TextUI\Configuration\Builder;
use Ahmedash95\PhpunitJsonFormatter\JsonExtension;

$builder = new Builder();
// ... configure your test suite

$configuration = $builder->build();
$configuration->extensions()->registerExtension(new JsonExtension());
```

## Output Format

The extension outputs JSON data in the following format:

```json
{
  "summary": {
    "totalTests": 10,
    "passed": 8,
    "failed": 1,
    "skipped": 1
  },
  "tests": [
    {
      "status": "passed",
      "id": "ExampleTest::testSomething",
      "className": "ExampleTest",
      "methodName": "testSomething",
      "message": null,
      "trace": [],
      "time": 0.0123
    },
    {
      "status": "failed",
      "id": "ExampleTest::testFailing",
      "className": "ExampleTest",
      "methodName": "testFailing",
      "message": "Failed asserting that false is true.",
      "trace": [
        "ExampleTest.php:15",
        "... more stack trace lines ..."
      ],
      "time": 0.0045
    }
    // ... more test results
  ]
}
```

## Features

- Suppresses PHPUnit's standard output
- Provides detailed information about each test
- Includes test execution time
- Formats error messages and stack traces for failed tests

## License

MIT
