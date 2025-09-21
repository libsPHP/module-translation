<?php
namespace NativeMind\Translation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TranslationStatus implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'pending', 'label' => __('Pending Translation')],
            ['value' => 'translated', 'label' => __('Translated')],
            ['value' => 'manual', 'label' => __('Manual Translation')],
            ['value' => 'error', 'label' => __('Translation Error')]
        ];
    }
}
