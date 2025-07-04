<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Test\Unit\Model\Label\Text\Processors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Exceptions\TextRenderException;
use Amasty\Label\Model\Label\Text\ProcessorInterface;
use Amasty\Label\Model\Label\Text\Processors\NewForProcessor;
use Amasty\Label\Test\Unit\Traits\ObjectManagerTrait;
use Amasty\Label\Test\Unit\Traits\ReflectionTrait;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class NewForProcessorTest
 *
 * @see \Amasty\Label\Model\Label\Text\Processors\NewForProcessor
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class NewForProcessorTest extends TestCase
{
    use ObjectManagerTrait;
    use ReflectionTrait;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var LabelInterface
     */
    private $label;

    public function setUp(): void
    {
        $this->processor = $this->getObjectManager()->getObject(
            NewForProcessor::class
        );
        $this->label = $this->getMockForAbstractClass(LabelInterface::class);
        $this->product = $this->getMockForAbstractClass(ProductInterface::class);
    }

    /**
     * @covers       \Amasty\Label\Model\Label\Text\Processors\NewForProcessor::process
     * @dataProvider invalidDataProvider
     *
     * @param string $variable
     */
    public function testInvalidVariable(string $variable): void
    {
        $this->expectException(TextRenderException::class);
        $this->processor->getVariableValue($variable, $this->label, $this->product);
    }

    /**
     * @covers       \Amasty\Label\Model\Label\Text\Processors\NewForProcessor::process
     * @dataProvider possibleVariablesProvider
     *
     * @param array $expected
     */
    public function testGetPossibleVariables(array $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->processor->getAcceptableVariables()
        );
    }

    public function invalidDataProvider(): array
    {
        return [
            [
                'test'
            ],
            [
                'dd'
            ]
        ];
    }

    public function possibleVariablesProvider(): array
    {
        return [
          [
              [
                 NewForProcessor::NEW_FOR
              ]
          ]
        ];
    }
}
