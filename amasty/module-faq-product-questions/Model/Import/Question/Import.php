<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Import\Question;

use Amasty\Base\Model\Import\AbstractImport;
use Amasty\Base\Model\Import\Behavior\BehaviorProviderInterface;
use Amasty\Base\Model\Import\ImportCounter;
use Amasty\Base\Model\Import\Mapping\MappingInterface;
use Amasty\Base\Model\Import\Validation\EncodingValidator;
use Amasty\Base\Model\Import\Validation\ValidatorPoolInterface;
use Amasty\Base\Model\MagentoVersion;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;

class Import extends AbstractImport
{

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        $entityTypeCode,
        ValidatorPoolInterface $validatorPool,
        BehaviorProviderInterface $behaviorProvider,
        MappingInterface $mapping,
        EncodingValidator $encodingValidator,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceConnection $resource,
        Context $context,
        MagentoVersion $magentoVersion = null,
        ImportCounter $importCounter = null
    ) {
        parent::__construct(
            $entityTypeCode,
            $validatorPool,
            $behaviorProvider,
            $mapping,
            $encodingValidator,
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $errorAggregator,
            $resource,
            [],
            $magentoVersion,
            $importCounter
        );
        $this->authorization = $context->getAuthorization();
    }

    public function isImportAllowed(): bool
    {
        return $this->authorization->isAllowed('Amasty_Faq::faq_import');
    }
}
