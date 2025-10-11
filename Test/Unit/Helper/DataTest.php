<?php
namespace NativeMind\Translation\Test\Unit\Helper;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;
use NativeMind\Translation\Model\TranslationCacheFactory;
use NativeMind\Translation\Model\ApiUsageTrackingFactory;
use NativeMind\Translation\Model\TranslationLogFactory;
use NativeMind\Translation\Model\TranslationCache;
use NativeMind\Translation\Model\ApiUsageTracking;
use NativeMind\Translation\Model\TranslationLog;

/**
 * @covers \NativeMind\Translation\Helper\Data
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;

    /**
     * @var TranslationCacheFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheFactoryMock;

    /**
     * @var ApiUsageTrackingFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $trackingFactoryMock;

    /**
     * @var TranslationLogFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logFactoryMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->cacheFactoryMock = $this->createMock(TranslationCacheFactory::class);
        $this->trackingFactoryMock = $this->createMock(ApiUsageTrackingFactory::class);
        $this->logFactoryMock = $this->createMock(TranslationLogFactory::class);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = new Data(
            $this->contextMock,
            $this->storeManagerMock,
            $this->cacheFactoryMock,
            $this->trackingFactoryMock,
            $this->logFactoryMock
        );
    }

    public function testIsTranslationEnabled()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                Data::XML_PATH_TRANSLATION_ENABLED,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $result = $this->helper->isTranslationEnabled();
        $this->assertTrue($result);
    }

    public function testIsTranslationDisabled()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->willReturn(false);

        $result = $this->helper->isTranslationEnabled();
        $this->assertFalse($result);
    }

    public function testGetGoogleApiKey()
    {
        $expectedKey = 'test-google-api-key';
        
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Data::XML_PATH_GOOGLE_API_KEY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($expectedKey);

        $result = $this->helper->getGoogleApiKey();
        $this->assertEquals($expectedKey, $result);
    }

    public function testGetOpenAiApiKey()
    {
        $expectedKey = 'test-openai-api-key';
        
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Data::XML_PATH_OPENAI_API_KEY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($expectedKey);

        $result = $this->helper->getOpenAiApiKey();
        $this->assertEquals($expectedKey, $result);
    }

    public function testGetTranslationService()
    {
        $expectedService = 'google';
        
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Data::XML_PATH_TRANSLATION_SERVICE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($expectedService);

        $result = $this->helper->getTranslationService();
        $this->assertEquals($expectedService, $result);
    }

    public function testGetStoreLocale()
    {
        $storeId = 1;
        $expectedLocale = 'ru_RU';
        
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('getConfig')
            ->with('general/locale/code')
            ->willReturn($expectedLocale);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $result = $this->helper->getStoreLocale($storeId);
        $this->assertEquals($expectedLocale, $result);
    }

    public function testTranslateTextReturnsEmptyForEmptyInput()
    {
        $result = $this->helper->translateText('', 'ru_RU', 1);
        $this->assertEquals('', $result);
    }

    public function testGetFromCacheReturnsNullWhenCacheNotFound()
    {
        $cacheMock = $this->createMock(TranslationCache::class);
        $cacheMock->method('getId')->willReturn(null);
        
        $this->cacheFactoryMock->method('create')->willReturn($cacheMock);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getFromCache');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, 'test', 'ru_RU', 'google');
        $this->assertNull($result);
    }

    public function testGetFromCacheReturnsTranslationWhenFound()
    {
        $expectedTranslation = 'тест';
        
        $cacheMock = $this->createMock(TranslationCache::class);
        $cacheMock->method('getId')->willReturn(1);
        $cacheMock->method('isExpired')->willReturn(false);
        $cacheMock->method('getData')->with('translated_text')->willReturn($expectedTranslation);
        $cacheMock->method('incrementHitCount')->willReturnSelf();
        $cacheMock->method('save')->willReturnSelf();
        
        $this->cacheFactoryMock->method('create')->willReturn($cacheMock);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getFromCache');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, 'test', 'ru_RU', 'google');
        $this->assertEquals($expectedTranslation, $result);
    }

    public function testGetFromCacheReturnsNullWhenExpired()
    {
        $cacheMock = $this->createMock(TranslationCache::class);
        $cacheMock->method('getId')->willReturn(1);
        $cacheMock->method('isExpired')->willReturn(true);
        
        $this->cacheFactoryMock->method('create')->willReturn($cacheMock);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getFromCache');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, 'test', 'ru_RU', 'google');
        $this->assertNull($result);
    }

    public function testSaveToCache()
    {
        $originalText = 'test';
        $translatedText = 'тест';
        $targetLanguage = 'ru_RU';
        $service = 'google';
        
        $cacheMock = $this->createMock(TranslationCache::class);
        $cacheMock->expects($this->once())->method('setData')->willReturnSelf();
        $cacheMock->expects($this->once())->method('save')->willReturnSelf();
        
        $this->cacheFactoryMock->method('create')->willReturn($cacheMock);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('saveToCache');
        $method->setAccessible(true);

        $method->invoke($this->helper, $originalText, $translatedText, $targetLanguage, $service, 1);
        $this->assertTrue(true); // If no exception thrown, test passes
    }

    public function testIsCacheEnabled()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                Data::XML_PATH_CACHE_ENABLED,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('isCacheEnabled');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, null);
        $this->assertTrue($result);
    }

    public function testGetCacheLifetime()
    {
        $expectedLifetime = 86400;
        
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Data::XML_PATH_CACHE_LIFETIME,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($expectedLifetime);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getCacheLifetime');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, null);
        $this->assertEquals($expectedLifetime, $result);
    }

    public function testGetCacheLifetimeReturnsDefault()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getCacheLifetime');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, null);
        $this->assertEquals(Data::DEFAULT_CACHE_LIFETIME, $result);
    }

    public function testGetMaxTextLength()
    {
        $expectedLength = 50000;
        
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Data::XML_PATH_MAX_TEXT_LENGTH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($expectedLength);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('getMaxTextLength');
        $method->setAccessible(true);

        $result = $method->invoke($this->helper, null);
        $this->assertEquals($expectedLength, $result);
    }

    public function testTrackApiUsage()
    {
        $trackingMock = $this->createMock(ApiUsageTracking::class);
        $trackingMock->expects($this->once())->method('track')->willReturnSelf();
        $trackingMock->expects($this->once())->method('calculateCostEstimate')->willReturn(0.001);
        $trackingMock->expects($this->once())->method('setData')->willReturnSelf();
        $trackingMock->expects($this->once())->method('save')->willReturnSelf();
        
        $this->trackingFactoryMock->method('create')->willReturn($trackingMock);

        $logMock = $this->createMock(TranslationLog::class);
        $logMock->method('setData')->willReturnSelf();
        $logMock->method('save')->willReturnSelf();
        
        $this->logFactoryMock->method('create')->willReturn($logMock);

        $reflection = new \ReflectionClass($this->helper);
        $method = $reflection->getMethod('trackApiUsage');
        $method->setAccessible(true);

        $method->invoke($this->helper, 'google', 'translate', ['character_count' => 100]);
        $this->assertTrue(true); // If no exception thrown, test passes
    }
}




