<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Block;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\Label\GetLabelImageUrlInterface;
use Amasty\Label\Model\ConfigProvider;
use Amasty\Label\Model\Label\Actions\GetLabelCssClass;
use Amasty\Label\Model\Label\Parts\FrontendSettings;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Label extends Template implements IdentityInterface
{
    public const DISPLAY_PRODUCT  = 'display/product';
    public const DISPLAY_CATEGORY = 'display/category';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Label::label.phtml';

    /**
     * @var LabelInterface
     */
    private $label;

    /**
     * @var Serializer
     */
    private $jsonEncoder;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var int
     */
    private $themeId;

    /**
     * @var GetLabelImageUrlInterface
     */
    private $getLabelImageUrl;

    /**
     * @var GetLabelCssClass
     */
    private $getLabelCssClass;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        Serializer $serializer,
        GetLabelImageUrlInterface $getLabelImageUrl,
        GetLabelCssClass $getLabelCssClass,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonEncoder = $serializer;
        $this->label = $data['label'] ?? null;
        $this->storeId = $this->_storeManager->getStore()->getId();
        $this->themeId = $this->_design->getDesignTheme()->getId();
        $this->getLabelImageUrl = $getLabelImageUrl;
        $this->getLabelCssClass = $getLabelCssClass;
        $this->addData([
            'cache_lifetime' => 86400
        ]);
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if ($this->getLabel() && isset($this->getLabel()->getExtensionAttributes()->getFrontendSettings()[0])) {
            $html .= parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $label = $this->getLabel();

        /** @var FrontendSettings $frontendSettings */
        $frontendSettings = $label->getExtensionAttributes()->getFrontendSettings()[0] ?? null;
        if ($frontendSettings === null) {
            return '{}';
        }

        $productId = $label->getExtensionAttributes()->getRenderSettings()->getProduct()->getId();
        $tooltip = $label->getExtensionAttributes()->getLabelTooltip();
        $textProcessor = $this->getData('text_processor');
        $tooltipContent = $textProcessor->renderLabelText($tooltip->getText(), $label, true);

        return $this->jsonEncoder->serialize([
            'position' => $this->getLabelCssClass->execute($frontendSettings->getPosition()),
            'size' => $frontendSettings->getImageSize(),
            'path' => $this->getContainerPath(),
            'mode' => $frontendSettings === Collection::MODE_LIST ? 'cat' : 'prod',
            'move' => (int) $label->getShouldMove(),
            'product' => $productId,
            'label' => $label->getLabelId(),
            'margin' => $this->configProvider->getMarginBetween(),
            'alignment' => $this->configProvider->getLabelAlignment(),
            'order' => $label->getPriority(),
            'redirect_url' => $frontendSettings->getRedirectUrl(),
            'tooltip' => [
                'backgroundColor' => $tooltip->getColor(),
                'color' => $tooltip->getTextColor(),
                'status' => $tooltip->getStatus(),
                'content' => rawurlencode($tooltipContent)
            ]
        ]);
    }

    public function getPriority(): int
    {
        return $this->getLabel()->getPriority();
    }

    public function isAdminArea(): bool
    {
        return $this->getArea() === 'adminhtml';
    }

    public function getCacheKeyInfo(): array
    {
        $productId = null;
        $renderingSettings = $this->getLabel()->getExtensionAttributes()->getRenderSettings();

        if ($renderingSettings->getProduct()) {
            $productId = $renderingSettings->getProduct()->getId();
        }

        return array_merge(parent::getCacheKeyInfo(), [
            'store_id' => $this->storeId,
            'theme_id' => $this->themeId,
            'label_id' => $this->getLabel()->getLabelId(),
            'label_mode' => $this->getLabelMode(),
            'product_id' => $productId,
            'move' => $this->getLabel()->getShouldMove()
        ]);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return $this->getLabel()->getIdentities();
    }

    public function setLabel(LabelInterface $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): LabelInterface
    {
        return $this->label;
    }

    private function getLabelMode(): int
    {
        $frontendSettings = $this->getLabel()->getExtensionAttributes()->getFrontendSettings()[0] ?? null;
        return isset($frontendSettings) ? $frontendSettings->getType() : 0;
    }

    public function getContainerPath(): ?string
    {
        if ($this->getLabelMode() == Collection::MODE_LIST) {
            $path = $this->configProvider->getProductListContainerPath();
        } else {
            $path = $this->configProvider->getProductContainerPath();
        }

        return $path;
    }

    /**
     * Get image url with mode and site url
     *
     * @return string
     */
    public function getImageSrc()
    {
        return $this->getLabelImageUrl->getByLabel($this->getLabel());
    }
}
