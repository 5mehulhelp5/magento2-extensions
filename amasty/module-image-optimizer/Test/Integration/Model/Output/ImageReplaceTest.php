<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Test\Integration\Model\Output;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ImageReplaceProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Module\Manager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ImageReplaceTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testImgReplaceLazyLoadEnabled(): void
    {
        $lazyConfigProviderMock = $this->createMock(LazyConfigProvider::class);
        $lazyConfigProviderMock->expects($this->any())->method('get')->willReturnCallback(function () {
            return new DataObject(['is_lazy' => true]);
        });
        $imageReplaceProcessor = $this->objectManager->create(
            ImageReplaceProcessor::class,
            ['lazyConfigProvider' => $lazyConfigProviderMock]
        );

        $content = file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_orig.html');
        $imageReplaceProcessor->process($content);
        $this->assertEquals(
            \file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_orig.html'),
            $content
        );
    }

    /**
     * @magentoAppIsolation enabled
     *
     * @magentoDataFixture Amasty_ImageOptimizer::Test/Integration/_files/image_with_optimizations.php
     *
     * @magentoConfigFixture current_store amoptimizer/images/replace_images_using_user_agent 0
     * @magentoConfigFixture current_store amoptimizer/replace_images_general/replace_strategy 1
     * @magentoConfigFixture current_store web/seo/use_rewrites 1
     * @magentoConfigFixture current_store web/unsecure/base_url http://test.com
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://test.com
     */
    public function testImgReplacePictureTag(): void
    {
        $this->executeReplaceProcessorAssertion(
            file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_picture.html')
        );
    }

    /**
     * @dataProvider replaceImgUserAgentDevicesDataProvider
     * @magentoAppIsolation enabled
     *
     * @magentoDataFixture Amasty_ImageOptimizer::Test/Integration/_files/image_with_optimizations.php
     *
     * @magentoConfigFixture current_store amoptimizer/images/replace_images_using_user_agent 1
     * @magentoConfigFixture current_store amoptimizer/replace_images_general/replace_strategy 1
     * @magentoConfigFixture current_store web/seo/use_rewrites 1
     * @magentoConfigFixture current_store web/unsecure/base_url http://test.com
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://test.com
     */
    public function testImgReplaceUserAgentDevices(string $userAgent, string $expectedResult): void
    {
        $_SERVER['HTTP_USER_AGENT'] = $userAgent;

        $this->executeReplaceProcessorAssertion($expectedResult);
    }

    /**
     * @magentoAppIsolation enabled
     *
     * @magentoDataFixture Amasty_ImageOptimizer::Test/Integration/_files/image_with_optimizations.php
     * @magentoDataFixture Amasty_ImageOptimizer::Test/Integration/_files/custom_algorithm.php
     *
     * @magentoConfigFixture current_store amoptimizer/images/replace_images_using_user_agent 0
     * @magentoConfigFixture current_store amoptimizer/replace_images_general/replace_strategy 3
     */
    public function testImgReplaceCustomAlgorithm(): void
    {
        $this->executeReplaceProcessorAssertion(
            \file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_custom.html')
        );
    }

    private function executeReplaceProcessorAssertion(string $expectedResult): void
    {
        $this->disableLazyLoadConfig();
        $imageReplaceProcessor = $this->objectManager->create(ImageReplaceProcessor::class);

        $content = file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_orig.html');
        $imageReplaceProcessor->process($content);
        $this->assertEquals(
            $expectedResult,
            $content
        );
    }

    private function disableLazyLoadConfig(): void
    {
        $this->objectManager->addSharedInstance(
            new class ($this->createMock(Manager::class),
                $this->createMock(ObjectManager::class),
                $this->createMock(DataObjectFactory::class)
            ) extends LazyConfigProvider {
                public function get(): DataObject
                {
                    return new DataObject();
                }
            },
            LazyConfigProvider::class,
            true
        );
    }

    public function replaceImgUserAgentDevicesDataProvider(): array
    {
        return [
            'webp' => [
                'Chrome',
                \file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_webp.html')
            ],
            'tablet' => [
                'iPad',
                \file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_tablet.html')
            ],
            'mobile' => [
                'iPhone',
                \file_get_contents(__DIR__ . '/../../_files/page_samples/sample_page_mobile.html')
            ]
        ];
    }
}
