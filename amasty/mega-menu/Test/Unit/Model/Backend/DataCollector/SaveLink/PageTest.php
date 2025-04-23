<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Test\Unit\Model\Backend\DataCollector\SaveLink;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\Backend\DataCollector\SaveLink\Page;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Test\Unit\Traits;

/**
 * Class PageTest
 * test page data collector
 *
 * @see Page
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PageTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::execute
     *
     * @dataProvider executeDataProvider
     *
     * @throws \ReflectionException
     */
    public function testExecute($data, $expectedResult)
    {
        $saveAction = $this->createPartialMock(Page::class, []);

        $actualResult = $saveAction->execute($data);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for execute test
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [
                [
                    LinkInterface::PAGE_ID => null,
                    ItemInterface::LINK_TYPE => UrlKey::LANDING_PAGE,
                    'landing_page' => 'test'
                ],
                [
                    ItemInterface::LINK_TYPE => UrlKey::LANDING_PAGE,
                    'landing_page' => 'test',
                    LinkInterface::PAGE_ID => 'test'
                ]
            ],
            [
                [
                    LinkInterface::PAGE_ID => 1,
                    ItemInterface::LINK_TYPE => UrlKey::LANDING_PAGE,
                    'landing_page' => 'test'
                ],
                [
                    ItemInterface::LINK_TYPE => UrlKey::LANDING_PAGE,
                    'landing_page' => 'test',
                    LinkInterface::PAGE_ID => 1
                ]
            ],
            [
                [
                    LinkInterface::PAGE_ID => null,
                    ItemInterface::LINK_TYPE => UrlKey::CMS_PAGE,
                    'landing_page' => 'test'
                ],
                [
                    ItemInterface::LINK_TYPE => UrlKey::NO,
                    'landing_page' => 'test',
                    LinkInterface::PAGE_ID => null
                ]
            ]
        ];
    }
}
