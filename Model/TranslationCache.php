<?php
namespace NativeMind\Translation\Model;

use Magento\Framework\Model\AbstractModel;

class TranslationCache extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\NativeMind\Translation\Model\ResourceModel\TranslationCache::class);
    }

    /**
     * Generate cache key
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $service
     * @return string
     */
    public function generateCacheKey($text, $targetLanguage, $service)
    {
        return md5($text . '|' . $targetLanguage . '|' . $service);
    }

    /**
     * Load by cache key
     *
     * @param string $cacheKey
     * @return $this
     */
    public function loadByCacheKey($cacheKey)
    {
        $this->_getResource()->load($this, $cacheKey, 'cache_key');
        return $this;
    }

    /**
     * Check if cache is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        $expiresAt = $this->getData('expires_at');
        if (!$expiresAt) {
            return false;
        }
        
        return strtotime($expiresAt) < time();
    }

    /**
     * Increment hit count
     *
     * @return $this
     */
    public function incrementHitCount()
    {
        $this->setData('hit_count', (int)$this->getData('hit_count') + 1);
        $this->setData('last_hit_at', date('Y-m-d H:i:s'));
        return $this;
    }
}

