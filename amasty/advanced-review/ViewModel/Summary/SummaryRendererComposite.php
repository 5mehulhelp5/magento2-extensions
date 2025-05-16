<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\ViewModel\Summary;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class SummaryRendererComposite implements SummaryRendererInterface
{
    public const RENDERER = 'renderer';
    public const SORT_ORDER = 'sortOrder';

    /**
     * @var array
     */
    private $rendererConfig;

    /**
     * @param array $rendererConfig
     *
     * @example [
     *      'renderer' => SummaryRendererInterface $someRenderer,
     *      'sortOrder' => int $sortOrder
     * ]
     */
    public function __construct(
        $rendererConfig = []
    ) {
        $this->rendererConfig = $rendererConfig;
    }

    public function render(ReviewCollection $collection, Product $product): string
    {
        return array_reduce(
            $this->getRenderers(),
            function (string $carry, SummaryRendererInterface $renderer) use ($collection, $product) {
                return $carry . $renderer->render($collection, $product);
            },
            ''
        );
    }

    private function getRenderers(): array
    {
        usort($this->rendererConfig, function (array $configA, array $configB): int {
            $sortOrderA = $configA[self::SORT_ORDER] ?? 0;
            $sortOrderB = $configB[self::SORT_ORDER] ?? 0;

            return $sortOrderA <=> $sortOrderB;
        });

        return array_reduce($this->rendererConfig, function (array $carry, array $config): array {
            $renderer = $config[self::RENDERER] ?? null;

            if ($renderer instanceof SummaryRendererInterface) {
                $carry[] = $renderer;
            }

            return $carry;
        }, []);
    }
}
