<?php
namespace NativeMind\Translation\Model\Data;

use NativeMind\Translation\Api\Data\TranslationResultInterface;
use Magento\Framework\DataObject;

class TranslationResult extends DataObject implements TranslationResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationId()
    {
        return $this->getData(self::TRANSLATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationId($translationId)
    {
        return $this->setData(self::TRANSLATION_ID, $translationId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalText()
    {
        return $this->getData(self::ORIGINAL_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalText($originalText)
    {
        return $this->setData(self::ORIGINAL_TEXT, $originalText);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedText()
    {
        return $this->getData(self::TRANSLATED_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatedText($translatedText)
    {
        return $this->setData(self::TRANSLATED_TEXT, $translatedText);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceLanguage()
    {
        return $this->getData(self::SOURCE_LANGUAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceLanguage($sourceLanguage)
    {
        return $this->setData(self::SOURCE_LANGUAGE, $sourceLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetLanguage()
    {
        return $this->getData(self::TARGET_LANGUAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetLanguage($targetLanguage)
    {
        return $this->setData(self::TARGET_LANGUAGE, $targetLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfidence()
    {
        return $this->getData(self::CONFIDENCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfidence($confidence)
    {
        return $this->setData(self::CONFIDENCE, $confidence);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
