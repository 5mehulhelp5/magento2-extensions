<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Block\Catalog\Product;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class Tab extends \Amasty\Faq\Block\AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->setData('sort_order', $this->configProvider->getTabPosition());
    }

    /**
     * @return int
     */
    public function getShortAnswerBehavior()
    {
        return (int)$this->configProvider->getProductPageShortAnswerBehavior();
    }

    /**
     * @return bool
     */
    public function isShowQuestionForm()
    {
        return $this->configProvider->isShowAskQuestionOnProductPage();
    }

    /**
     * @return bool
     */
    public function showAskQuestionForm()
    {
        return $this->configProvider->isShowAskQuestionOnProductPage();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $tabName = $this->configProvider->getTabName() ?: __('Product Questions') . ' {count}';
        if (strpos($tabName, '{count}') !== false) {
            $listBlock = $this->getChildBlock('amasty_faq_questions');
            $count = $listBlock ? $listBlock->getCollection()->count() : 0;
            $tabName = str_replace('{count}', (($count) ? '(' . $count .')' : ''), $tabName);
        }
        
        $this->setTitle($this->escapeHtml($tabName));

        return parent::_toHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Faq\Model\ResourceModel\Question\Collection::CACHE_TAG];
    }
}
