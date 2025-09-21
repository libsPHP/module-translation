<?php
namespace NativeMind\Translation\Model\Data;

use NativeMind\Translation\Api\Data\TranslationStatsInterface;
use Magento\Framework\DataObject;

class TranslationStats extends DataObject implements TranslationStatsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalProducts()
    {
        return $this->getData(self::TOTAL_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalProducts($totalProducts)
    {
        return $this->setData(self::TOTAL_PRODUCTS, $totalProducts);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedProducts()
    {
        return $this->getData(self::TRANSLATED_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatedProducts($translatedProducts)
    {
        return $this->setData(self::TRANSLATED_PRODUCTS, $translatedProducts);
    }

    /**
     * {@inheritdoc}
     */
    public function getPendingProducts()
    {
        return $this->getData(self::PENDING_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPendingProducts($pendingProducts)
    {
        return $this->setData(self::PENDING_PRODUCTS, $pendingProducts);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorProducts()
    {
        return $this->getData(self::ERROR_PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorProducts($errorProducts)
    {
        return $this->setData(self::ERROR_PRODUCTS, $errorProducts);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCategories()
    {
        return $this->getData(self::TOTAL_CATEGORIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCategories($totalCategories)
    {
        return $this->setData(self::TOTAL_CATEGORIES, $totalCategories);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedCategories()
    {
        return $this->getData(self::TRANSLATED_CATEGORIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatedCategories($translatedCategories)
    {
        return $this->setData(self::TRANSLATED_CATEGORIES, $translatedCategories);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastTranslationDate()
    {
        return $this->getData(self::LAST_TRANSLATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastTranslationDate($lastTranslationDate)
    {
        return $this->setData(self::LAST_TRANSLATION_DATE, $lastTranslationDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiUsage()
    {
        return $this->getData(self::API_USAGE) ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function setApiUsage($apiUsage)
    {
        return $this->setData(self::API_USAGE, $apiUsage);
    }
}
