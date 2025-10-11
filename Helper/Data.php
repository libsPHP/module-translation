<?php
namespace NativeMind\Translation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Model\TranslationCacheFactory;
use NativeMind\Translation\Model\ApiUsageTrackingFactory;
use NativeMind\Translation\Model\TranslationLogFactory;

class Data extends AbstractHelper
{
    const XML_PATH_TRANSLATION_ENABLED = 'nativelang/general/enabled';
    const XML_PATH_GOOGLE_API_KEY = 'nativelang/google/api_key';
    const XML_PATH_OPENAI_API_KEY = 'nativelang/openai/api_key';
    const XML_PATH_TRANSLATION_SERVICE = 'nativelang/general/service';
    const XML_PATH_CACHE_ENABLED = 'nativelang/general/cache_enabled';
    const XML_PATH_CACHE_LIFETIME = 'nativelang/general/cache_lifetime';
    const XML_PATH_MAX_TEXT_LENGTH = 'nativelang/general/max_text_length';

    const DEFAULT_TIMEOUT = 30;
    const DEFAULT_MAX_RETRIES = 3;
    const DEFAULT_CACHE_LIFETIME = 86400; // 24 hours

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TranslationCacheFactory
     */
    protected $translationCacheFactory;

    /**
     * @var ApiUsageTrackingFactory
     */
    protected $apiUsageTrackingFactory;

    /**
     * @var TranslationLogFactory
     */
    protected $translationLogFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param TranslationCacheFactory $translationCacheFactory
     * @param ApiUsageTrackingFactory $apiUsageTrackingFactory
     * @param TranslationLogFactory $translationLogFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TranslationCacheFactory $translationCacheFactory,
        ApiUsageTrackingFactory $apiUsageTrackingFactory,
        TranslationLogFactory $translationLogFactory
    ) {
        $this->storeManager = $storeManager;
        $this->translationCacheFactory = $translationCacheFactory;
        $this->apiUsageTrackingFactory = $apiUsageTrackingFactory;
        $this->translationLogFactory = $translationLogFactory;
        parent::__construct($context);
    }

    /**
     * Check if translation is enabled
     * 
     * @param int|null $storeId
     * @return bool
     */
    public function isTranslationEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRANSLATION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Google API Key
     * 
     * @param int|null $storeId
     * @return string
     */
    public function getGoogleApiKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get OpenAI API Key
     * 
     * @param int|null $storeId
     * @return string
     */
    public function getOpenAiApiKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OPENAI_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get translation service
     * 
     * @param int|null $storeId
     * @return string
     */
    public function getTranslationService($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRANSLATION_SERVICE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get locale code for store
     * 
     * @param int $storeId
     * @return string
     */
    public function getStoreLocale($storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        return $store->getConfig('general/locale/code');
    }

    /**
     * Translate text using configured service with caching
     * 
     * @param string $text
     * @param string $targetLanguage
     * @param int|null $storeId
     * @return string
     */
    public function translateText($text, $targetLanguage, $storeId = null)
    {
        if (empty($text)) {
            return '';
        }

        // Validate text length
        $maxLength = $this->getMaxTextLength($storeId);
        if (strlen($text) > $maxLength) {
            $this->log('error', 'Text exceeds maximum length', [
                'text_length' => strlen($text),
                'max_length' => $maxLength
            ]);
            return $text;
        }

        $service = $this->getTranslationService($storeId);
        
        // Check cache first
        if ($this->isCacheEnabled($storeId)) {
            $cachedTranslation = $this->getFromCache($text, $targetLanguage, $service);
            if ($cachedTranslation !== null) {
                $this->log('info', 'Translation retrieved from cache', [
                    'service' => $service,
                    'target_language' => $targetLanguage
                ]);
                return $cachedTranslation;
            }
        }

        // Translate with retry mechanism
        $translated = $this->translateWithRetry($text, $targetLanguage, $service, $storeId);

        // Save to cache
        if ($translated !== $text && $this->isCacheEnabled($storeId)) {
            $this->saveToCache($text, $translated, $targetLanguage, $service, $storeId);
        }

        return $translated;
    }

    /**
     * Translate with retry mechanism
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $service
     * @param int|null $storeId
     * @param int $attempt
     * @return string
     */
    protected function translateWithRetry($text, $targetLanguage, $service, $storeId = null, $attempt = 1)
    {
        try {
            switch ($service) {
                case 'google':
                    return $this->translateWithGoogle($text, $targetLanguage, $storeId);
                case 'openai':
                    return $this->translateWithOpenAI($text, $targetLanguage, $storeId);
                default:
                    return $this->translateWithGoogle($text, $targetLanguage, $storeId);
            }
        } catch (\Exception $e) {
            $this->log('error', 'Translation attempt failed', [
                'attempt' => $attempt,
                'service' => $service,
                'error' => $e->getMessage()
            ]);

            if ($attempt < self::DEFAULT_MAX_RETRIES) {
                // Exponential backoff
                sleep(pow(2, $attempt - 1));
                return $this->translateWithRetry($text, $targetLanguage, $service, $storeId, $attempt + 1);
            }

            return $text;
        }
    }

    /**
     * Translate text using Google Translate API with cURL
     * 
     * @param string $text
     * @param string $targetLanguage
     * @param int|null $storeId
     * @return string
     * @throws \Exception
     */
    private function translateWithGoogle($text, $targetLanguage, $storeId = null)
    {
        $apiKey = $this->getGoogleApiKey($storeId);
        if (empty($apiKey)) {
            throw new \Exception('Google API key is not configured');
        }

        $startTime = microtime(true);
        $url = "https://translation.googleapis.com/language/translate/v2";

        $data = [
            'key' => $apiKey,
            'target' => $targetLanguage,
            'q' => $text
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseTime = (microtime(true) - $startTime) * 1000; // milliseconds
        $error = curl_error($ch);
        curl_close($ch);

        // Track API usage
        $this->trackApiUsage('google', 'translate', [
            'request_size' => strlen($text),
            'response_size' => strlen($response),
            'character_count' => strlen($text),
            'response_time' => $responseTime,
            'status_code' => $httpCode,
            'success' => ($httpCode == 200),
            'error_message' => $error
        ]);

        if ($response === false || $httpCode != 200) {
            throw new \Exception("Google Translation API error: HTTP {$httpCode}, {$error}");
        }

        $responseData = json_decode($response, true);
        
        if (isset($responseData['data']['translations'][0]['translatedText'])) {
            return $responseData['data']['translations'][0]['translatedText'];
        }

        if (isset($responseData['error'])) {
            throw new \Exception("Google Translation API error: " . $responseData['error']['message']);
        }

        throw new \Exception('Invalid response from Google Translation API');
    }

    /**
     * Translate text using OpenAI API with cURL
     * 
     * @param string $text
     * @param string $targetLanguage
     * @param int|null $storeId
     * @return string
     * @throws \Exception
     */
    private function translateWithOpenAI($text, $targetLanguage, $storeId = null)
    {
        $apiKey = $this->getOpenAiApiKey($storeId);
        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is not configured');
        }

        $startTime = microtime(true);
        $url = "https://api.openai.com/v1/chat/completions";

        $data = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a professional translator. Translate the given text to {$targetLanguage} locale. Only return the translated text, nothing else."
                ],
                [
                    "role" => "user",
                    "content" => $text
                ]
            ],
            "max_tokens" => 2048,
            "temperature" => 0.3
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseTime = (microtime(true) - $startTime) * 1000; // milliseconds
        $error = curl_error($ch);
        curl_close($ch);

        // Track API usage
        $this->trackApiUsage('openai', 'translate', [
            'request_size' => strlen(json_encode($data)),
            'response_size' => strlen($response),
            'character_count' => strlen($text),
            'response_time' => $responseTime,
            'status_code' => $httpCode,
            'success' => ($httpCode == 200),
            'error_message' => $error
        ]);

        if ($response === false || $httpCode != 200) {
            throw new \Exception("OpenAI API error: HTTP {$httpCode}, {$error}");
        }

        $responseData = json_decode($response, true);
        
        if (isset($responseData['choices'][0]['message']['content'])) {
            return trim($responseData['choices'][0]['message']['content']);
        }

        if (isset($responseData['error'])) {
            throw new \Exception("OpenAI API error: " . $responseData['error']['message']);
        }

        throw new \Exception('Invalid response from OpenAI API');
    }

    /**
     * Get from cache
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $service
     * @return string|null
     */
    protected function getFromCache($text, $targetLanguage, $service)
    {
        try {
            /** @var \NativeMind\Translation\Model\TranslationCache $cache */
            $cache = $this->translationCacheFactory->create();
            $cacheKey = $cache->generateCacheKey($text, $targetLanguage, $service);
            $cache->loadByCacheKey($cacheKey);

            if ($cache->getId() && !$cache->isExpired()) {
                $cache->incrementHitCount()->save();
                return $cache->getData('translated_text');
            }
        } catch (\Exception $e) {
            $this->log('warning', 'Cache retrieval failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Save to cache
     *
     * @param string $originalText
     * @param string $translatedText
     * @param string $targetLanguage
     * @param string $service
     * @param int|null $storeId
     * @return void
     */
    protected function saveToCache($originalText, $translatedText, $targetLanguage, $service, $storeId = null)
    {
        try {
            /** @var \NativeMind\Translation\Model\TranslationCache $cache */
            $cache = $this->translationCacheFactory->create();
            $cacheKey = $cache->generateCacheKey($originalText, $targetLanguage, $service);

            $cache->setData([
                'cache_key' => $cacheKey,
                'original_text' => $originalText,
                'translated_text' => $translatedText,
                'target_language' => $targetLanguage,
                'translation_service' => $service,
                'expires_at' => date('Y-m-d H:i:s', time() + $this->getCacheLifetime($storeId)),
                'hit_count' => 0
            ]);

            $cache->save();
        } catch (\Exception $e) {
            $this->log('warning', 'Cache save failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Track API usage
     *
     * @param string $service
     * @param string $operationType
     * @param array $data
     * @return void
     */
    protected function trackApiUsage($service, $operationType, array $data = [])
    {
        try {
            /** @var \NativeMind\Translation\Model\ApiUsageTracking $tracking */
            $tracking = $this->apiUsageTrackingFactory->create();
            $tracking->track($service, $operationType, $data);

            // Calculate cost estimate
            if (isset($data['character_count'])) {
                $cost = $tracking->calculateCostEstimate($service, $data['character_count']);
                $tracking->setData('cost_estimate', $cost);
            }

            $tracking->save();
        } catch (\Exception $e) {
            $this->log('warning', 'API usage tracking failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log($level, $message, array $context = [])
    {
        try {
            /** @var \NativeMind\Translation\Model\TranslationLog $log */
            $log = $this->translationLogFactory->create();
            $log->setData([
                'level' => $level,
                'message' => $message,
                'context' => json_encode($context),
                'operation' => 'translation'
            ]);
            $log->save();
        } catch (\Exception $e) {
            // Silent fail for logging
        }

        // Also log to Magento logger
        $this->_logger->log($level, $message, $context);
    }

    /**
     * Check if cache is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    protected function isCacheEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CACHE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get cache lifetime in seconds
     *
     * @param int|null $storeId
     * @return int
     */
    protected function getCacheLifetime($storeId = null)
    {
        $lifetime = $this->scopeConfig->getValue(
            self::XML_PATH_CACHE_LIFETIME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $lifetime ?: self::DEFAULT_CACHE_LIFETIME;
    }

    /**
     * Get max text length
     *
     * @param int|null $storeId
     * @return int
     */
    protected function getMaxTextLength($storeId = null)
    {
        $maxLength = $this->scopeConfig->getValue(
            self::XML_PATH_MAX_TEXT_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $maxLength ?: 50000; // Default 50KB
    }
}
