<?php
namespace NativeMind\Translation\Api\Data;

/**
 * Translation result interface
 */
interface TranslationResultInterface
{
    const TRANSLATION_ID = 'translation_id';
    const ORIGINAL_TEXT = 'original_text';
    const TRANSLATED_TEXT = 'translated_text';
    const SOURCE_LANGUAGE = 'source_language';
    const TARGET_LANGUAGE = 'target_language';
    const CONFIDENCE = 'confidence';
    const STATUS = 'status';
    const ERROR_MESSAGE = 'error_message';
    const CREATED_AT = 'created_at';

    /**
     * Get translation ID
     *
     * @return string
     */
    public function getTranslationId();

    /**
     * Set translation ID
     *
     * @param string $translationId
     * @return $this
     */
    public function setTranslationId($translationId);

    /**
     * Get original text
     *
     * @return string
     */
    public function getOriginalText();

    /**
     * Set original text
     *
     * @param string $originalText
     * @return $this
     */
    public function setOriginalText($originalText);

    /**
     * Get translated text
     *
     * @return string|null
     */
    public function getTranslatedText();

    /**
     * Set translated text
     *
     * @param string $translatedText
     * @return $this
     */
    public function setTranslatedText($translatedText);

    /**
     * Get source language
     *
     * @return string|null
     */
    public function getSourceLanguage();

    /**
     * Set source language
     *
     * @param string $sourceLanguage
     * @return $this
     */
    public function setSourceLanguage($sourceLanguage);

    /**
     * Get target language
     *
     * @return string
     */
    public function getTargetLanguage();

    /**
     * Set target language
     *
     * @param string $targetLanguage
     * @return $this
     */
    public function setTargetLanguage($targetLanguage);

    /**
     * Get confidence score
     *
     * @return float|null
     */
    public function getConfidence();

    /**
     * Set confidence score
     *
     * @param float $confidence
     * @return $this
     */
    public function setConfidence($confidence);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Set error message
     *
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
