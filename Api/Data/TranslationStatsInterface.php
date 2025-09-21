<?php
namespace NativeMind\Translation\Api\Data;

/**
 * Translation statistics interface
 */
interface TranslationStatsInterface
{
    const STORE_ID = 'store_id';
    const TOTAL_PRODUCTS = 'total_products';
    const TRANSLATED_PRODUCTS = 'translated_products';
    const PENDING_PRODUCTS = 'pending_products';
    const ERROR_PRODUCTS = 'error_products';
    const TOTAL_CATEGORIES = 'total_categories';
    const TRANSLATED_CATEGORIES = 'translated_categories';
    const LAST_TRANSLATION_DATE = 'last_translation_date';
    const API_USAGE = 'api_usage';

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get total products
     *
     * @return int
     */
    public function getTotalProducts();

    /**
     * Set total products
     *
     * @param int $totalProducts
     * @return $this
     */
    public function setTotalProducts($totalProducts);

    /**
     * Get translated products
     *
     * @return int
     */
    public function getTranslatedProducts();

    /**
     * Set translated products
     *
     * @param int $translatedProducts
     * @return $this
     */
    public function setTranslatedProducts($translatedProducts);

    /**
     * Get pending products
     *
     * @return int
     */
    public function getPendingProducts();

    /**
     * Set pending products
     *
     * @param int $pendingProducts
     * @return $this
     */
    public function setPendingProducts($pendingProducts);

    /**
     * Get error products
     *
     * @return int
     */
    public function getErrorProducts();

    /**
     * Set error products
     *
     * @param int $errorProducts
     * @return $this
     */
    public function setErrorProducts($errorProducts);

    /**
     * Get total categories
     *
     * @return int
     */
    public function getTotalCategories();

    /**
     * Set total categories
     *
     * @param int $totalCategories
     * @return $this
     */
    public function setTotalCategories($totalCategories);

    /**
     * Get translated categories
     *
     * @return int
     */
    public function getTranslatedCategories();

    /**
     * Set translated categories
     *
     * @param int $translatedCategories
     * @return $this
     */
    public function setTranslatedCategories($translatedCategories);

    /**
     * Get last translation date
     *
     * @return string|null
     */
    public function getLastTranslationDate();

    /**
     * Set last translation date
     *
     * @param string $lastTranslationDate
     * @return $this
     */
    public function setLastTranslationDate($lastTranslationDate);

    /**
     * Get API usage statistics
     *
     * @return array
     */
    public function getApiUsage();

    /**
     * Set API usage statistics
     *
     * @param array $apiUsage
     * @return $this
     */
    public function setApiUsage($apiUsage);
}
