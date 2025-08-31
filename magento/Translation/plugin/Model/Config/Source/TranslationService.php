<?php
namespace NativeMind\Translation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TranslationService implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'google', 'label' => __('Google Translate')],
            ['value' => 'openai', 'label' => __('OpenAI GPT')]
        ];
    }
}
