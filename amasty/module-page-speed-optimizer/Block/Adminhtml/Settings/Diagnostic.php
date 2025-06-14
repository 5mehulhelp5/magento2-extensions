<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Url;

class Diagnostic extends CommonInfoField
{
    public const CUSTOM_OPTIMIZATION_URL = 'https://products.amasty.com/magento-optimization' .
    '?utm_source=extension&utm_medium=backend&utm_campaign=gpso_optimization_service_m2';

    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var string
     */
    protected $_template = 'Amasty_PageSpeedOptimizer::diagnostic.phtml';

    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    public function __construct(
        LocaleResolver $localeResolver,
        Template\Context $context,
        Url $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->localeResolver = $localeResolver;
    }

    public function getFrontendUrl(): string
    {
        if ($storeId = $this->getRequest()->getParam('store')) {
            $url = $this->urlBuilder->getUrl(null, ['_scope' => $storeId]);
        } else {
            $url = parent::getBaseUrl();
        }

        return $url;
    }

    public function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $columns = $this->getColspanHtmlAttr();

        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

    public function getCustomOptimizationUrl(): string
    {
        return self::CUSTOM_OPTIMIZATION_URL;
    }
}
