# Unit Tests for NativeMind Translation Module

## Overview

This directory contains PHPUnit unit tests for the NativeMind Translation module. The tests ensure code quality, reliability, and maintainability.

## Test Coverage

### Helper Tests
- **DataTest.php** - Tests for translation helper
  - Configuration methods
  - Translation service selection
  - Cache operations
  - API tracking
  - Validation logic

### Model Tests
- **TranslationCacheTest.php** - Cache model functionality
  - Cache key generation
  - Expiration logic
  - Hit count tracking
  
- **TranslationQueueTest.php** - Queue model operations
  - Retry mechanism
  - Status transitions
  - Error handling
  
- **ApiUsageTrackingTest.php** - API usage tracking
  - Cost calculations (Google/OpenAI)
  - Usage statistics
  - Data recording

### Consumer Tests
- **ProductTranslationConsumerTest.php** - Message queue consumer
  - Message processing
  - Error handling
  - History recording

### Data Model Tests
- **TranslationMessageTest.php** - Translation message DTO
  - Getters/setters
  - Type casting
  - Method chaining
  
- **TranslationBatchMessageTest.php** - Batch message DTO
  - Array handling
  - Data validation

### Cron Tests
- **CleanCacheTest.php** - Cache cleaning job
  - Expired entries removal
  - Error handling
  
- **CleanQueueTest.php** - Queue cleaning job
  - Completed items removal
  - Logging

## Running Tests

### All Tests
```bash
cd /path/to/magento/app/code/NativeMind/Translation
vendor/bin/phpunit
```

### Specific Test File
```bash
vendor/bin/phpunit Test/Unit/Helper/DataTest.php
```

### Specific Test Method
```bash
vendor/bin/phpunit --filter testIsTranslationEnabled Test/Unit/Helper/DataTest.php
```

### With Coverage Report
```bash
vendor/bin/phpunit --coverage-html ./Test/coverage-html
```

Then open `Test/coverage-html/index.html` in your browser.

### From Magento Root
```bash
cd /path/to/magento
php bin/magento dev:tests:run unit NativeMind_Translation
```

## Test Statistics

| Category | Files | Tests | Assertions |
|----------|-------|-------|------------|
| Helper | 1 | 20+ | 50+ |
| Models | 3 | 25+ | 60+ |
| Consumers | 1 | 2 | 10+ |
| Data Models | 2 | 10+ | 25+ |
| Cron | 2 | 6 | 15+ |
| **Total** | **10** | **65+** | **160+** |

## Coverage Goals

- **Target Coverage:** 80%+
- **Critical Components:** 90%+
  - Helper/Data.php
  - TranslationManagement.php
  - Consumers
  - Queue operations

## Writing New Tests

### Test Naming Convention
```php
// ✅ Good
public function testMethodNameDoesSpecificThing()
public function testMethodNameReturnsExpectedValue()
public function testMethodNameThrowsExceptionOnError()

// ❌ Bad
public function test1()
public function testMethod()
```

### Test Structure
```php
public function testSomething()
{
    // 1. Arrange - Setup test data and mocks
    $expectedValue = 'test';
    $mock->method('getValue')->willReturn($expectedValue);
    
    // 2. Act - Call the method being tested
    $result = $this->model->doSomething();
    
    // 3. Assert - Verify the result
    $this->assertEquals($expectedValue, $result);
}
```

### Mock Best Practices
```php
// ✅ Use createMock for interfaces and classes
$mock = $this->createMock(SomeInterface::class);

// ✅ Set expectations
$mock->expects($this->once())
    ->method('doSomething')
    ->with($this->equalTo('value'))
    ->willReturn('result');

// ✅ Use willReturnMap for multiple return values
$mock->method('getData')
    ->willReturnMap([
        ['key1', null, 'value1'],
        ['key2', null, 'value2']
    ]);
```

## CI/CD Integration

### GitHub Actions Example
```yaml
name: Unit Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: vendor/bin/phpunit
      - name: Coverage
        run: vendor/bin/phpunit --coverage-clover coverage.xml
```

## Troubleshooting

### Common Issues

**Issue:** Tests fail with "Class not found"
```bash
# Solution: Regenerate autoload
composer dump-autoload
```

**Issue:** Mock expectations not met
```bash
# Solution: Check method call order and parameters
$mock->expects($this->exactly(2))  // Exact number of calls
    ->method('doSomething')
    ->withConsecutive(             // Order matters!
        [$param1],
        [$param2]
    );
```

**Issue:** Coverage report not generated
```bash
# Solution: Install Xdebug
pecl install xdebug
```

## Test Maintenance

### Adding Tests for New Features
1. Create test file in `Test/Unit/` matching source structure
2. Extend `PHPUnit\Framework\TestCase`
3. Add `@covers` annotation
4. Write tests for all public methods
5. Aim for edge cases and error conditions

### Updating Tests
- Update tests when changing method signatures
- Add tests for new edge cases discovered
- Remove tests for deprecated functionality
- Keep test data realistic

## Best Practices

### ✅ DO
- Test one thing per test method
- Use descriptive test names
- Mock external dependencies
- Test edge cases and errors
- Keep tests independent
- Use data providers for similar tests

### ❌ DON'T
- Test private methods directly
- Use real database connections
- Depend on test execution order
- Write overly complex tests
- Skip test documentation
- Ignore failing tests

## Code Quality Metrics

### Test Quality Indicators
- All tests pass: ✅
- Coverage > 80%: ✅
- No skipped tests: ✅
- Fast execution (< 30s): ✅
- No warnings: ✅

## Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Magento Testing Guide](https://developer.adobe.com/commerce/testing/)
- [Best Practices](https://phpunit.readthedocs.io/en/latest/writing-tests-for-phpunit.html)

---

**Maintained by:** NativeMind Team  
**Last Updated:** 2024  
**Status:** ✅ Active

