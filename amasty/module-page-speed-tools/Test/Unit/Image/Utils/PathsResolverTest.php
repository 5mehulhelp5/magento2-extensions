<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Test\Unit\Image\Utils;

use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\Common;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;
use Amasty\PageSpeedTools\Model\Image\Utils\PathsResolver;
use PHPUnit\Framework\TestCase;

class PathsResolverTest extends TestCase
{
    /**
     * @var PathsResolver
     */
    private $pathResolver;

    protected function setUp(): void
    {
        $this->pathResolver = new PathsResolver();
    }

    /**
     * @dataProvider resolveDataProvider
     * @covers PathsResolver::resolve
     */
    public function testResolve(
        ReplaceConfigInterface $replaceConfig,
        array $images,
        array $expectedResult
    ): void {
        $result = $this->pathResolver->resolve($replaceConfig, $images, 0);
        $this->assertEquals($expectedResult, $result);
    }

    private function resolveDataProvider(): array
    {
        return [
            'simple match with one attribute' => [
                $this->createConfiguredMock(
                    Common::class,
                    [
                        'getGroupByName' => ['test' => 2],
                        'isReplaceAllAttrs' => false
                    ]
                ),
                ['test' => ['matched'], 1 => ['matched 2'], 2 => ['matched 3']],
                ['matched 3']
            ],
            'match with empty result' => [
                $this->createConfiguredMock(
                    Common::class,
                    [
                        'getGroupByName' => ['test' => 2],
                        'isReplaceAllAttrs' => false
                    ]
                ),
                ['test' => [''], 1 => [''], 2 => ['']],
                []
            ],
            'match with all attrs' => [
                $this->createConfiguredMock(
                    Common::class,
                    [
                        'getGroupByName' => ['test1' => 1, 'test2' => 2],
                        'isReplaceAllAttrs' => true
                    ]
                ),
                ['test1' => ['match 1'], 1 => ['match 2'], 'test2' => ['match 3'], 2 => ['match 4']],
                ['match 2', 'match 4']
            ]
        ];
    }
}
