<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Test\Unit\Image;

use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\ReplaceAlgorithmInterface;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithmResolver;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReplaceAlgorithmResolverTest extends TestCase
{
    /**
     * @covers ReplaceAlgorithmResolver::initializeReplaceAlgorithms
     * @dataProvider initializationDataProvider
     */
    public function testInitialization(array $customProcessors, bool $isError): void
    {
        $dummyAlgorithmMock = $this->createReplaceAlgorithmMock('dummy');
        if ($isError) {
            $this->expectException(\LogicException::class);
        }

        new ReplaceAlgorithmResolver($dummyAlgorithmMock, $customProcessors);
        $this->assertTrue(true); //cases when no exception is thrown
    }

    /**
     * @covers ReplaceAlgorithmResolver::resolve
     * @dataProvider resolveDataProvider
     */
    public function testResolve(
        array $customProcessors,
        ReplaceConfigInterface $pattern,
        string $expectedAlgorithmName
    ): void {
        $dummyAlgorithmMock = $this->createReplaceAlgorithmMock('dummy');
        $algorithmResolver = new ReplaceAlgorithmResolver($dummyAlgorithmMock, $customProcessors);

        $algorithm = $algorithmResolver->resolve($pattern);
        $this->assertEquals($expectedAlgorithmName, $algorithm->getAlgorithmName());
    }

    /**
     * @return ReplaceAlgorithmInterface|MockObject
     */
    private function createReplaceAlgorithmMock(
        string $algorithmName = 'test',
        bool $isAvailable = true,
        bool $canOverride = false
    ) {
        $algorithmMock = $this->createMock(ReplaceAlgorithmInterface::class);

        $algorithmMock->expects($this->any())->method('execute')->willReturnArgument(1);
        $algorithmMock->expects($this->any())->method('getReplaceImagePath')->willReturnArgument(0);
        $algorithmMock->expects($this->any())->method('getAlgorithmName')->willReturn($algorithmName);
        $algorithmMock->expects($this->any())->method('isAvailable')->willReturn($isAvailable);
        $algorithmMock->expects($this->any())->method('canOverride')->willReturn($canOverride);

        return $algorithmMock;
    }

    private function initializationDataProvider(): array
    {
        return [
            'no custom processors' => [[], false],
            'correct custom processors' => [
                [$this->createReplaceAlgorithmMock('test1'), $this->createReplaceAlgorithmMock('test2')],
                false
            ],
            'wrong class of custom processor' => [
                [$this->createReplaceAlgorithmMock('test1'), 'string'],
                true
            ]
        ];
    }

    private function resolveDataProvider(): array
    {
        $patternWithDefault = $this->createMock(ReplaceConfigInterface::class);
        $patternWithDefault->expects($this->any())->method('getBaseAlgorithm')->willReturn('default');

        $patternWithoutDefault = $this->createMock(ReplaceConfigInterface::class);

        return [
            'no default, expects first available' => [
                [
                    'first' => $this->createReplaceAlgorithmMock('first', false),
                    'seconde' => $this->createReplaceAlgorithmMock('second')
                ],
                $patternWithoutDefault,
                'second'
            ],
            'have default, expects default' => [
                [
                    'first' => $this->createReplaceAlgorithmMock('first'),
                    'default' => $this->createReplaceAlgorithmMock('default')
                ],
                $patternWithDefault,
                'default'
            ],
            'have default, have override, expects override' => [
                [
                    'default' => $this->createReplaceAlgorithmMock('default', false),
                    'second' => $this->createReplaceAlgorithmMock('second', true, true)
                ],
                $patternWithDefault,
                'second'
            ],
            'have default, but it not exists, expects dummy' => [
                ['first' => $this->createReplaceAlgorithmMock('first')],
                $patternWithDefault,
                'dummy'
            ],
            'have unavailable default, no overrides, expect default' => [
                [
                    'first' => $this->createReplaceAlgorithmMock('first'),
                    'default' => $this->createReplaceAlgorithmMock('default', false)
                ],
                $patternWithDefault,
                'default'
            ]
        ];
    }
}
