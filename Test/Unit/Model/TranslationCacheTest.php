<?php
namespace NativeMind\Translation\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\TranslationCache;
use NativeMind\Translation\Model\ResourceModel\TranslationCache as TranslationCacheResource;

/**
 * @covers \NativeMind\Translation\Model\TranslationCache
 */
class TranslationCacheTest extends TestCase
{
    /**
     * @var TranslationCache
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = $this->getMockBuilder(TranslationCache::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'setData', 'save', '_getResource', 'load'])
            ->getMock();
    }

    public function testGenerateCacheKey()
    {
        // Create a real instance for this test
        $model = new TranslationCache(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $text = 'Hello World';
        $targetLanguage = 'ru_RU';
        $service = 'google';

        $expectedKey = md5($text . '|' . $targetLanguage . '|' . $service);
        $actualKey = $model->generateCacheKey($text, $targetLanguage, $service);

        $this->assertEquals($expectedKey, $actualKey);
    }

    public function testGenerateCacheKeyConsistency()
    {
        $model = new TranslationCache(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $text = 'Test';
        $language = 'en_US';
        $service = 'openai';

        $key1 = $model->generateCacheKey($text, $language, $service);
        $key2 = $model->generateCacheKey($text, $language, $service);

        $this->assertEquals($key1, $key2);
    }

    public function testIsExpiredReturnsFalseWhenNoExpiration()
    {
        $this->model->method('getData')
            ->with('expires_at')
            ->willReturn(null);

        $result = $this->model->isExpired();
        $this->assertFalse($result);
    }

    public function testIsExpiredReturnsTrueWhenExpired()
    {
        $pastDate = date('Y-m-d H:i:s', time() - 3600);
        
        $this->model->method('getData')
            ->with('expires_at')
            ->willReturn($pastDate);

        $result = $this->model->isExpired();
        $this->assertTrue($result);
    }

    public function testIsExpiredReturnsFalseWhenNotExpired()
    {
        $futureDate = date('Y-m-d H:i:s', time() + 3600);
        
        $this->model->method('getData')
            ->with('expires_at')
            ->willReturn($futureDate);

        $result = $this->model->isExpired();
        $this->assertFalse($result);
    }

    public function testIncrementHitCount()
    {
        $currentHitCount = 5;
        
        $this->model->expects($this->once())
            ->method('getData')
            ->with('hit_count')
            ->willReturn($currentHitCount);

        $this->model->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                ['hit_count', $currentHitCount + 1],
                [$this->anything(), $this->anything()]
            )
            ->willReturnSelf();

        $result = $this->model->incrementHitCount();
        $this->assertSame($this->model, $result);
    }
}

