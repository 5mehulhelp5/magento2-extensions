<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Elastic Search Base for Magento 2
 */

namespace Amasty\ElasticSearch\Model\Indexer\Structure;

use Amasty\ElasticSearch\Api\Data\Indexer\Structure\AnalyzerBuilderInterface;
use Amasty\ElasticSearch\Model\Indexer\Structure\AnalyserBuilderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AnalyserBuilder implements AnalyzerBuilderInterface
{
    /**
     * @var AnalyserBuilderFactory
     */
    private $analyserBuilderFactory;

    /**
     * @var \Amasty\ElasticSearch\Model\Config
     */
    private $config;

    public function __construct(
        AnalyserBuilderFactory $analyserBuilderFactory,
        \Amasty\ElasticSearch\Model\Config $config
    ) {
        $this->analyserBuilderFactory = $analyserBuilderFactory;
        $this->config = $config;
    }

    /**
     * @param int $storeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($storeId)
    {
        $analyser = $this->analyserBuilderFactory->create(
            $this->config->getAnalyzerType($storeId)
        );
        return $analyser->build($storeId);
    }
}
