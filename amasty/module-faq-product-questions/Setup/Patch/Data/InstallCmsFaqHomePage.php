<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallCmsFaqHomePage implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var TypeListInterface
     */
    private $typeList;

    public function __construct(
        ResourceInterface $moduleResource,
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository,
        WriterInterface $configWriter,
        TypeListInterface $typeList
    ) {
        $this->moduleResource = $moduleResource;
        $this->configWriter = $configWriter;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->typeList = $typeList;
    }

    public function apply()
    {
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_Faq');
        if (!$setupDataVersion || version_compare($setupDataVersion, '2.0.0', '<')) {
            /** @var \Magento\Cms\Api\Data\PageInterface $page */
            $page = $this->pageFactory->create();
            $page->setIsActive(1);
            $page->setTitle('FAQ Home Page');
            $page->setIdentifier('amasty-faq-home-page');
            $page->setContent($this->getHomePageContent());
            $page->setPageLayout('1column');
            $page->setStoreId(["0"]);
            try {
                $page = $this->pageRepository->save($page);
                $this->configWriter->save(
                    ConfigProvider::PATH_PREFIX . ConfigProvider::FAQ_CMS_HOME_PAGE,
                    $page->getId()
                );
                $this->typeList->invalidate(Config::TYPE_IDENTIFIER);
            } catch (\Exception $e) {
                null;
            }
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    private function getHomePageContent()
    {
        return preg_replace("/\r|\n/", "", '
            <p>{{widget
                type="Amasty\Faq\Block\Widgets\BackToProduct"
                back_to_product_align="am-widget-right"
            }}</p>
            <div class="page-title-wrapper">
                <h1 class="page-title" style="text-align: center;">
                    <span class="base" data-ui-id="page-title-wrapper">Help Center</span>
                </h1>
            </div>
            <p>{{widget
                type="Amasty\Faq\Block\Widgets\SearchBox"
                search_box_width="40%"
                search_box_align="am-widget-center"
            }}</p>
            <p> </p>
            <p>{{widget
                type="Amasty\Faq\Block\Widgets\Categories"
                layout_type="am-widget-categories-3"
                questions_limit="10"
                categories_limit="9"
                without_questions="0"
                sort_categories_by="position"
                sort_questions_by="position"
                short_answer_behavior="0"}}</p>
            <p>{{widget
                type="Amasty\Faq\Block\Widgets\TagsBlock"
                tags_limit="20"}}</p>
        ');
    }
}
