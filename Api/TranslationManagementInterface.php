<?php
namespace NativeMind\Translation\Api;

/**
 * Translation management interface
 */
interface TranslationManagementInterface
{
    /**
     * Translate text
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string|null $sourceLanguage
     * @param int|null $storeId
     * @return \NativeMind\Translation\Api\Data\TranslationResultInterface
     */
    public function translateText($text, $targetLanguage, $sourceLanguage = null, $storeId = null);

    /**
     * Translate multiple texts
     *
     * @param string[] $texts
     * @param string $targetLanguage
     * @param string|null $sourceLanguage
     * @param int|null $storeId
     * @return \NativeMind\Translation\Api\Data\TranslationResultInterface[]
     */
    public function translateTexts($texts, $targetLanguage, $sourceLanguage = null, $storeId = null);

    /**
     * Translate product
     *
     * @param int $productId
     * @param int $storeId
     * @param bool $force
     * @return \NativeMind\Translation\Api\Data\TranslationResultInterface
     */
    public function translateProduct($productId, $storeId, $force = false);

    /**
     * Translate multiple products
     *
     * @param int[] $productIds
     * @param int $storeId
     * @param bool $force
     * @return \NativeMind\Translation\Api\Data\TranslationResultInterface[]
     */
    public function translateProducts($productIds, $storeId, $force = false);

    /**
     * Get translation status
     *
     * @param string $translationId
     * @return \NativeMind\Translation\Api\Data\TranslationStatusInterface
     */
    public function getTranslationStatus($translationId);

    /**
     * Get translation statistics
     *
     * @param int|null $storeId
     * @return \NativeMind\Translation\Api\Data\TranslationStatsInterface
     */
    public function getTranslationStats($storeId = null);
}
