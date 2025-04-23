<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Block\Product;

use Amasty\MegaMenu\Block\Product\Widget\Html\Pager as MegaMenuPager;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\AbstractBlock;

class ProductsSlider extends ProductsList implements IdentityInterface
{
    public const AM_MEGA_MENU_IMAGES = 'am_mega_menu_widget_image';

    /**
     * @var string
     */
    protected $_template = 'Amasty_MegaMenu::product/widget/content/grid.phtml';

    /**
     * Default slider layout
     */
    public const DEFAULT_BLOCK_LAYOUT = 'grid';

    /**
     * Default display options state
     */
    public const DEFAULT_DISPLAY_OPTIONS = false;

    /**
     * Default slick slider slidesToShow
     */
    public const DEFAULT_SLIDES_TO_SHOW = 3;

    /**
     * Default slick slider autoplay
     */
    public const DEFAULT_SLIDER_AUTOPLAY = false;

    /**
     * Default slick slider autoplay speed
     */
    public const DEFAULT_SLIDER_AUTOPLAY_SPEED = 2000;

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPagerHtml()
    {
        $result = '';
        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            $pager = $this->getPager();
            if ($pager instanceof AbstractBlock) {
                $result = $pager->toHtml();
            }
        }

        return $result;
    }

    /**
     * @return \Magento\Catalog\Block\Product\Widget\Html\Pager|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPager()
    {
        if (!$this->pager) {
            $this->pager = $this->getLayout()->createBlock(
                MegaMenuPager::class,
                $this->getWidgetPagerName()
            );

            $this->pager->setUseContainer(true)
                ->setShowAmounts(true)
                ->setShowPerPage(false)
                ->setPageVarName($this->getData('page_var_name'))
                ->setLimit($this->getProductsPerPage())
                ->setTotalLimit($this->getProductsCount())
                ->setCollection($this->getProductCollection());
        }

        return $this->pager;
    }

    /**
     * @return mixed
     */
    public function getBlockLayout()
    {
        if (!$this->hasData('block_layout')) {
            $this->setData('block_layout', self::DEFAULT_BLOCK_LAYOUT);
        }

        return $this->getData('block_layout');
    }

    /**
     * @return bool
     */
    public function getProductOptionsState()
    {
        if (!$this->hasData('display_options')) {
            $this->setData('display_options', self::DEFAULT_DISPLAY_OPTIONS);
        }

        return (bool)$this->getData('display_options');
    }

    /**
     * @return int
     */
    public function getSlidesToShow()
    {
        if (!$this->hasData('slider_items_show')) {
            $this->setData('slider_items_show', self::DEFAULT_SLIDES_TO_SHOW);
        }

        return (int)$this->getData('slider_items_show');
    }

    /**
     * @return float
     */
    public function getSliderWidth()
    {
        return (float)$this->getData('slider_width');
    }

    /**
     * @return bool
     */
    public function getAutoplayState()
    {
        if (!$this->hasData('slider_autoplay')) {
            $this->setData('slider_autoplay', self::DEFAULT_SLIDER_AUTOPLAY);
        }

        return (bool)$this->getData('slider_autoplay');
    }

    /**
     * @return int
     */
    public function getAutoplaySpeed()
    {
        if (!$this->hasData('slider_autoplay_speed')) {
            $this->setData('slider_autoplay_speed', self::DEFAULT_SLIDER_AUTOPLAY_SPEED);
        }

        return $this->getData('slider_autoplay_speed');
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return str_replace('\\', '-', $this->getNameInLayout());
    }

    /**
     * Get widget block name
     * see parent:getWidgetPagerBlockName()
     *
     * @return string
     */
    private function getWidgetPagerName(): string
    {
        $pageName = $this->getData('page_var_name');
        $pagerBlockName = 'widget.products.list.pager';

        if (!$pageName) {
            return $pagerBlockName;
        }

        return $pagerBlockName . '.' . $pageName;
    }
}
