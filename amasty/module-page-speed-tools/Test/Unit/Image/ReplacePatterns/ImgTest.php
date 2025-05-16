<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Test\Unit\Image\ReplacePatterns;

use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\Img;
use PHPUnit\Framework\TestCase;

class ImgTest extends TestCase
{
    /**
     * @covers Img::updatePatternAndGroup
     */
    public function testInitializationWithoutAttributes()
    {
        $classExample = new class extends Img {
            public function retrieveImgAttributes(): array
            {
                return [];
            }
        };

        $this->assertEquals(
            '<img\s*(?:(?<src>src\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)|'
            . '(?<any>[a-z\-_]+\s*\=\s*[\'\"](.*?)[\'\"].*?\s*))+.*?>',
            $classExample->getPattern()
        );
        $this->assertEquals(
            ['src' => 2],
            $classExample->getGroupByName()
        );
    }

    /**
     * @covers Img::updatePatternAndGroup
     */
    public function testInitializationWithAttributes()
    {
        $classExample = new class extends Img {
            public function retrieveImgAttributes(): array
            {
                return ['data-src', 'lazy-attr'];
            }
        };

        $this->assertEquals(
            '<img\s*(?:(?<data_src>data-src\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)|'
            . '(?<lazy_attr>lazy-attr\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)'
            . '|(?<src>src\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)'
            . '|(?<any>[a-z\-_]+\s*\=\s*[\'\"](.*?)[\'\"].*?\s*))+.*?>',
            $classExample->getPattern()
        );
        $this->assertEquals(
            ['data_src' => 2, 'lazy_attr' => 4, 'src' => 6],
            $classExample->getGroupByName()
        );
    }
}
