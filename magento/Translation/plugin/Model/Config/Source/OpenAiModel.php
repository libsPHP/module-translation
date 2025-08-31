<?php
namespace NativeMind\Translation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class OpenAiModel implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'gpt-3.5-turbo', 'label' => __('GPT-3.5 Turbo')],
            ['value' => 'gpt-4', 'label' => __('GPT-4')],
            ['value' => 'gpt-4-turbo-preview', 'label' => __('GPT-4 Turbo')]
        ];
    }
}
