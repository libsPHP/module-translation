<?php
namespace NativeMind\Translation\Model;

use Magento\Framework\Model\AbstractModel;

class ApiUsageTracking extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\NativeMind\Translation\Model\ResourceModel\ApiUsageTracking::class);
    }

    /**
     * Track API call
     *
     * @param string $service
     * @param string $operationType
     * @param array $data
     * @return $this
     */
    public function track($service, $operationType, array $data = [])
    {
        $this->setData('service', $service);
        $this->setData('operation_type', $operationType);
        
        if (isset($data['request_size'])) {
            $this->setData('request_size', $data['request_size']);
        }
        if (isset($data['response_size'])) {
            $this->setData('response_size', $data['response_size']);
        }
        if (isset($data['character_count'])) {
            $this->setData('character_count', $data['character_count']);
        }
        if (isset($data['response_time'])) {
            $this->setData('response_time', $data['response_time']);
        }
        if (isset($data['status_code'])) {
            $this->setData('status_code', $data['status_code']);
        }
        if (isset($data['success'])) {
            $this->setData('success', $data['success']);
        }
        if (isset($data['error_message'])) {
            $this->setData('error_message', $data['error_message']);
        }
        if (isset($data['cost_estimate'])) {
            $this->setData('cost_estimate', $data['cost_estimate']);
        }
        if (isset($data['metadata'])) {
            $this->setData('metadata', json_encode($data['metadata']));
        }
        
        return $this;
    }

    /**
     * Calculate cost estimate based on service and usage
     *
     * @param string $service
     * @param int $characterCount
     * @return float
     */
    public function calculateCostEstimate($service, $characterCount)
    {
        // Google Translate pricing: $20 per 1M characters
        // OpenAI pricing varies by model, approximate $0.002 per 1K tokens (~4K chars)
        
        if ($service === 'google') {
            return ($characterCount / 1000000) * 20;
        } elseif ($service === 'openai') {
            return ($characterCount / 4000) * 0.002;
        }
        
        return 0;
    }
}




