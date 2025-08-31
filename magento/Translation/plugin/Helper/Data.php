<?php
namespace NativeMind\Translation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_TRANSLATION_ENABLED = 'nativelang/general/enabled';
    const XML_PATH_GOOGLE_API_KEY = 'nativelang/google/api_key';
    const XML_PATH_OPENAI_API_KEY = 'nativelang/openai/api_key';
    const XML_PATH_TRANSLATION_SERVICE = 'nativelang/general/service';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
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
     * Translate text using configured service
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

        $service = $this->getTranslationService($storeId);
        
        switch ($service) {
            case 'google':
                return $this->translateWithGoogle($text, $targetLanguage, $storeId);
            case 'openai':
                return $this->translateWithOpenAI($text, $targetLanguage, $storeId);
            default:
                return $this->translateWithGoogle($text, $targetLanguage, $storeId);
        }
    }

    /**
     * Translate text using Google Translate API
     * 
     * @param string $text
     * @param string $targetLanguage
     * @param int|null $storeId
     * @return string
     */
    private function translateWithGoogle($text, $targetLanguage, $storeId = null)
    {
        $apiKey = $this->getGoogleApiKey($storeId);
        if (empty($apiKey)) {
            return $text;
        }

        $text = urlencode($text);
        $url = "https://translation.googleapis.com/language/translate/v2?key={$apiKey}&target={$targetLanguage}&q={$text}";
        
        try {
            $response = file_get_contents($url);
            $response = json_decode($response, true);
            
            if (isset($response['data']['translations'][0]['translatedText'])) {
                return $response['data']['translations'][0]['translatedText'];
            }
        } catch (\Exception $e) {
            $this->_logger->error('Google Translation Error: ' . $e->getMessage());
        }
        
        return $text;
    }

    /**
     * Translate text using OpenAI API
     * 
     * @param string $text
     * @param string $targetLanguage
     * @param int|null $storeId
     * @return string
     */
    private function translateWithOpenAI($text, $targetLanguage, $storeId = null)
    {
        $apiKey = $this->getOpenAiApiKey($storeId);
        if (empty($apiKey)) {
            return $text;
        }

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

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\nAuthorization: Bearer " . $apiKey,
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = file_get_contents("https://api.openai.com/v1/chat/completions", false, $context);
            
            if ($result !== false) {
                $result = json_decode($result, true);
                if (isset($result['choices'][0]['message']['content'])) {
                    return trim($result['choices'][0]['message']['content']);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error('OpenAI Translation Error: ' . $e->getMessage());
        }

        return $text;
    }
}
