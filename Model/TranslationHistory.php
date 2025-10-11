<?php
namespace NativeMind\Translation\Model;

use Magento\Framework\Model\AbstractModel;

class TranslationHistory extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\NativeMind\Translation\Model\ResourceModel\TranslationHistory::class);
    }

    /**
     * Get history ID
     *
     * @return int|null
     */
    public function getHistoryId()
    {
        return $this->getData('history_id');
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->getData('entity_type');
    }

    /**
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getData('attribute_code');
    }

    /**
     * Get original text
     *
     * @return string|null
     */
    public function getOriginalText()
    {
        return $this->getData('original_text');
    }

    /**
     * Get translated text
     *
     * @return string|null
     */
    public function getTranslatedText()
    {
        return $this->getData('translated_text');
    }

    /**
     * Get source language
     *
     * @return string|null
     */
    public function getSourceLanguage()
    {
        return $this->getData('source_language');
    }

    /**
     * Get target language
     *
     * @return string
     */
    public function getTargetLanguage()
    {
        return $this->getData('target_language');
    }

    /**
     * Get translation service
     *
     * @return string
     */
    public function getTranslationService()
    {
        return $this->getData('translation_service');
    }

    /**
     * Get confidence score
     *
     * @return float|null
     */
    public function getConfidenceScore()
    {
        return $this->getData('confidence_score');
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->getData('error_message');
    }

    /**
     * Get processing time
     *
     * @return int|null
     */
    public function getProcessingTime()
    {
        return $this->getData('processing_time');
    }

    /**
     * Get user ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->getData('user_id');
    }
}




