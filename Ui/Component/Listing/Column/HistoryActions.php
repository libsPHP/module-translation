<?php
namespace NativeMind\Translation\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class HistoryActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['history_id'])) {
                    $item[$name]['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'nativelang/translation/viewHistory',
                            ['id' => $item['history_id']]
                        ),
                        'label' => __('View Details')
                    ];
                    
                    if ($item['status'] === 'failed') {
                        $item[$name]['retry'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'nativelang/translation/retry',
                                ['id' => $item['history_id']]
                            ),
                            'label' => __('Retry')
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}




