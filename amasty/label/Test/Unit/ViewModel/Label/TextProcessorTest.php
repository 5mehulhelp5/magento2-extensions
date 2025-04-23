<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Test\Unit\ViewModel\Label;

use Amasty\Label\Api\Data\LabelExtensionInterface;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\Data\RenderSettingsInterface;
use Amasty\Label\Model\ConfigProvider;
use Amasty\Label\Model\Label\AltTag\Processors\LabelNameProcessor;
use Amasty\Label\Model\Label\AltTag\Processors\ProductNameProcessor;
use Amasty\Label\Model\Label\Text\ProcessorInterface;
use Amasty\Label\Model\Label\Text\VariableProcessorInterface;
use Amasty\Label\Model\Label\Text\ZeroValueCheckerInterface;
use Amasty\Label\Test\Unit\Traits\ObjectManagerTrait;
use Amasty\Label\Test\Unit\Traits\ReflectionTrait;
use Amasty\Label\ViewModel\Label\TextProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Escaper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @see \Amasty\Label\ViewModel\Label\TextProcessor
 */
class TextProcessorTest extends TestCase
{
    /**
     * @var VariableProcessorInterface
     */
    private $variableProcessor;

    /**
     * @var LabelNameProcessor
     */
    private $labelNameProcessor;

    /**
     * @var ProductNameProcessor
     */
    private $productNameProcessor;

    /**
     * @var TextProcessor
     */
    private $processor;

    public function setUp(): void
    {
        $this->variableProcessor = $this->createMock(VariableProcessorInterface::class);
        $defaultProcessor = $this->createMock(ProcessorInterface::class);
        $zeroValueChecker = $this->createMock(ZeroValueCheckerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $configProvider = $this->createMock(ConfigProvider::class);
        $escaper = $this->createMock(Escaper::class);

        $this->labelNameProcessor = $this->createMock(LabelNameProcessor::class);
        $this->labelNameProcessor->expects($this->any())
            ->method('getAcceptableVariables')
            ->willReturn([LabelNameProcessor::LABEL_NAME]);

        $this->productNameProcessor = $this->createMock(ProductNameProcessor::class);
        $this->productNameProcessor->expects($this->any())
            ->method('getAcceptableVariables')
            ->willReturn([ProductNameProcessor::PRODUCT_NAME]);

        $this->processor = new TextProcessor(
            $this->variableProcessor,
            $defaultProcessor,
            $configProvider,
            $zeroValueChecker,
            $escaper,
            $logger,
            [],
            [
                ['processor' => $this->labelNameProcessor],
                ['processor' => $this->productNameProcessor]
            ]
        );
    }

    /**
     * @covers TextProcessor::renderLabelAltTag
     * @dataProvider renderLabelAltTagDataProvider
     *
     * @param string $text
     * @param array $variables
     * @param string $expectedResult
     */
    public function testRenderLabelAltTag(string $text, array $variables, string $expectedResult): void
    {
        $product = $this->createMock(ProductInterface::class);

        $settings = $this->createMock(RenderSettingsInterface::class);
        $settings->expects($this->any())
            ->method('getProduct')
            ->willReturn($product);

        $labelExtention = $this->getMockBuilder(LabelExtensionInterface::class)
            ->addMethods(['getRenderSettings'])
            ->getMock();
        $labelExtention->expects($this->any())
            ->method('getRenderSettings')
            ->willReturn($settings);

        $label = $this->createMock(LabelInterface::class);
        $label->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($labelExtention);

        $this->labelNameProcessor->expects($this->any())
            ->method('getVariableValue')
            ->willReturnCallback(
                function ($variable) {
                    return $variable;
                }
            );

        $this->productNameProcessor->expects($this->any())
            ->method('getVariableValue')
            ->willReturnCallback(
                function ($variable) {
                    return $variable;
                }
            );

        $this->variableProcessor->expects($this->any())
            ->method('extractVariables')
            ->willReturn($variables);
        $this->variableProcessor->expects($this->any())
            ->method('insertVariable')
            ->willReturnCallback(
                function ($text, $variable, $variableValue) {
                    return str_replace("{{$variable}}", $variableValue, $text);
                }
            );

        $this->assertEquals(
            $expectedResult,
            $this->processor->renderLabelAltTag($text, $label)
        );
    }

    /**
     * Data provider for renderLabelAltTag test
     * @return array
     */
    public function renderLabelAltTagDataProvider(): array
    {
        return [
            ['test', [], 'test'],
            ['tes{undefined_variable}t', ['undefined_variable'], 'test'],
            ['{product_name}', ['product_name'] ,'product_name'],
            ['{label_name}', ['label_name'], 'label_name'],
            ['{label_name} {product_name}', ['label_name', 'product_name'], 'label_name product_name']
        ];
    }
}
