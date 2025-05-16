<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\Component;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Listing extends \Magento\Ui\Component\Listing
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var string
     */
    private $componentName;

    public function __construct(
        EncoderInterface $encoder,
        DecoderInterface $decoder,
        ContextInterface $context,
        $components = [],
        array $data = [],
        ?string $componentName = null // TODO move to not optional
    ) {
        parent::__construct($context, $components, $data);
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->componentName = $componentName ?? 'amreports_sales_overview_columns';
    }

    /**
     * @return string
     */
    public function render()
    {
        $result = parent::render();
        if (is_string($result)) {
            $result = $this->decoder->decode($result);
            $result = $this->castToColumnFormats($result);
            $result = $this->encoder->encode($result);
        }

        return $result;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function castToColumnFormats(array $result): array
    {
        if (!isset($result['totals'])) {
            return $result;
        }

        $columnsComponent = $this->getComponent($this->componentName);
        $dataSource = [
            'data' => [
                'items' => [
                    $result['totals']
                ]
            ]
        ];

        foreach ($columnsComponent->getChildComponents() as $component) {
            $dataSource = $component->prepareDataSource($dataSource);
        }

        $result['totals'] = $dataSource['data']['items'][0];

        return $result;
    }
}
